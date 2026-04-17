<?php

namespace SURF\Services;

use SURF\Core\PostTypes\BasePost;
use SURF\Core\Taxonomies\Taxonomy;
use SURF\PostTypes\Asset;
use SURF\PostTypes\Faq;
use WP_Term;

/**
 * Class PostTypeSitemapService
 * Builds a sitemap of categories and posts for a given post type
 * @package SURF\Services
 */
class PostTypeSitemapService
{

	/**
	 * Gets a full sitemap for a post type
	 * @param null|class-string<BasePost> $cpt_class
	 * @param bool $hide_empty
	 * @param bool $primary_only
	 * @return array
	 */
	public static function build( ?string $cpt_class = null, bool $hide_empty = true, bool $primary_only = false ): array
	{
		// Validate CPT class
		if ( !static::isValidCPTClass( $cpt_class ) ) {
			return [];
		}

		// Get the category taxonomy associated with this CPT
		$taxonomy = $cpt_class::getCategoryTaxonomy();
		if ( empty( $taxonomy ) ) {
			return [];
		}

		// Fetch all terms for this taxonomy
		$term_list = Taxonomy::query( [
			'taxonomy'   => $taxonomy,
			'hide_empty' => $hide_empty,
			'orderby'    => 'name',
			'order'      => 'ASC',
		] );
		if ( $term_list->isEmpty() ) {
			return [];
		}

		// Fetch all posts for this post type
		$post_list = $cpt_class::query( [
			'post_type'      => $cpt_class::getName(),
			'posts_per_page' => -1,
			'orderby'        => 'title',
			'order'          => 'ASC',
		] )->toArray();
		if ( $hide_empty && empty( $post_list ) ) {
			return [];
		}

		// Map posts per term_id for faster lookup
		$posts_by_term = [];
		$uncategorized = []; // Collect posts with no terms
		foreach ( $post_list as $post ) {
			$post_id    = $post->ID;
			$post_terms = wp_get_post_terms( $post_id, $taxonomy );
			if ( empty( $post_terms ) || is_wp_error( $post_terms ) ) {
				$uncategorized[] = $post;
				continue;
			}

			// Get term IDs for this post (easier to work with in some places)
			$post_term_ids = wp_list_pluck( $post_terms, 'term_id' );

			// Use only Yoast-primary
			if ( $primary_only ) {
				$primary_id = (int) get_post_meta( $post_id, '_yoast_wpseo_primary_' . $taxonomy, true );
				if ( !empty( $primary_id ) ) {
					if ( in_array( $primary_id, $post_term_ids, true ) ) {
						if ( !isset( $posts_by_term[ $primary_id ] ) ) {
							$posts_by_term[ $primary_id ] = [];
						}
						$posts_by_term[ $primary_id ][] = $post;
						continue;
					}
				}

				// No primary > pick the deepest term
				$deepest_term = static::getDeepestTerm( $post_terms );
				if ( !empty( $deepest_term ) ) {
					if ( !isset( $posts_by_term[ $deepest_term->term_id ] ) ) {
						$posts_by_term[ $deepest_term->term_id ] = [];
					}
					$posts_by_term[ $deepest_term->term_id ][] = $post;
					continue;
				}
			}

			// Multiple terms > assign to all
			foreach ( $post_term_ids as $term_id ) {
				if ( !isset( $posts_by_term[ $term_id ] ) ) {
					$posts_by_term[ $term_id ] = [];
				}
				$posts_by_term[ $term_id ][] = $post;
			}
		}

		// Build the main tree
		$tree = static::buildTermTree( $term_list, $posts_by_term );

		// Add uncategorized posts at the end, if any
		if ( !empty( $uncategorized ) ) {
			$pseudo_term = (object) [
				'term_id'          => 0,
				'slug'             => 'uncategorized',
				'name'             => __( 'Other content', 'wp-surf-theme' ),
				'parent'           => 0,
				'is_uncategorized' => true, // flag for renderer
			];

			$tree['uncategorized'] = [
				'term'     => $pseudo_term,
				'title'    => $pseudo_term->name,
				'children' => [],
				'posts'    => $uncategorized,
			];
		}

		return $tree;
	}

	/**
	 * Validates if a given CPT class is allowed and properly structured for sitemap generation
	 * @param null|string $cpt_class
	 * @return bool
	 */
	public static function isValidCPTClass( ?string $cpt_class = null ): bool
	{
		if ( empty( $cpt_class ) ) {
			return false;
		}

		if ( !is_subclass_of( $cpt_class, BasePost::class ) ) {
			return false;
		}

		if ( !method_exists( $cpt_class, 'getCategoryTaxonomy' ) ) {
			return false;
		}

		if ( !in_array( $cpt_class, static::listAllowedPostTypes(), true ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Picks the deepest term from an array of WP_Term objects
	 * @param WP_Term[] $terms
	 * @return WP_Term|null
	 */
	public static function getDeepestTerm( array $terms ): ?object
	{
		// Sort terms by depth (parent > child) and pick the one with the largest depth
		usort( $terms, function ( $a, $b )
		{
			$depth_a = static::getTermDepth( $a );
			$depth_b = static::getTermDepth( $b );

			return $depth_b <=> $depth_a;
		} );

		return $terms[0] ?? null;
	}

	/**
	 * Gets depth of term by walking up parent chain
	 * @param object $term
	 * @return int
	 */
	public static function getTermDepth( object $term ): int
	{
		$depth = 0;
		while ( $term->parent ) {
			$parent = get_term( $term->parent );
			if ( is_wp_error( $parent ) || !$parent ) {
				break;
			}
			$term = $parent;
			$depth++;
		}

		return $depth;
	}

	/**
	 * Recursively builds term tree
	 * @param iterable $terms
	 * @param array $posts_by_term
	 * @param int $parent_id
	 * @return array
	 */
	public static function buildTermTree( iterable $terms, array $posts_by_term, int $parent_id = 0 ): array
	{
		$tree = [];

		foreach ( $terms as $term ) {
			if ( (int) $term->parent !== $parent_id ) {
				continue;
			}

			$tree[ $term->slug ] = [
				'term'     => $term,
				'title'    => $term->name,
				'children' => static::buildTermTree( $terms, $posts_by_term, $term->term_id ),
				'posts'    => $posts_by_term[ $term->term_id ] ?? [],
			];
		}

		return $tree;
	}

	/**
	 * Lists all allowed CPT classes
	 * @return array<class-string<BasePost>>
	 */
	public static function listAllowedPostTypes(): array
	{
		// You can add more allowed CPTs via this filter
		return apply_filters( 'surf_sitemap_allowed', [
			Asset::class,
			Faq::class,
		] );
	}

}
