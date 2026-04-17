<?php

namespace SURF\Helpers;

use Exception;
use SURF\Core\Exceptions\MismatchingPostTypesException;
use SURF\Core\Taxonomies\Taxonomy;
use SURF\Core\Taxonomies\TermCollection;
use SURF\Enums\Theme;
use SURF\PostTypes\ContactPerson;
use WP_Error;
use WP_Term;

/**
 * Post helper methods
 * Class PostHelper
 * @package SURF\Helpers
 */
class PostHelper
{

	/**
	 * @param int $excerptLength
	 * @param int $id
	 * @param string $excerptMore
	 * @return string
	 */
	public static function getMyExcerpt( $excerptLength = 55, $id = 0, $excerptMore = ' [...]' )
	{
		if ( !empty( $id ) ) {
			$the_post = get_post( $id );
			$text     = ( $the_post->post_excerpt ) ? $the_post->post_excerpt : $the_post->post_content;
		} else {
			global $post;
			$text = ( $post->post_excerpt ) ? $post->post_excerpt : get_the_content( '' );
		}

		$text = strip_shortcodes( $text );
		$text = wpautop( do_shortcode( $text ) );
		$text = str_replace( ']]>', ']]&gt;', $text );
		$text = wp_strip_all_tags( $text );

		$words = preg_split( "/[\n\r\t ]+/", $text, $excerptLength + 1, PREG_SPLIT_NO_EMPTY );

		if ( count( $words ) > $excerptLength ) {
			array_pop( $words );
			$text = implode( ' ', $words );
			$text = $text . $excerptMore;
		} else {
			$text = implode( ' ', $words );
		}

		return $text;
	}

	/**
	 * @param int|null $postId
	 * @param string|null $taxonomy
	 * @return WP_Term|WP_Error|bool|array|null
	 */
	public static function getPrimaryTerm( ?int $postId = 0, ?string $taxonomy = '' ): WP_Term|WP_Error|bool|array|null
	{
		$postId = $postId ?: get_the_ID();

		/* Get the primary category ID from the corresponding meta key */
		$primaryCategory = get_post_meta( $postId, "_yoast_wpseo_primary_{$taxonomy}", true );

		/* If there is no primary category, retrieve 'regular' categories */
		if ( !$primaryCategory ) {
			$categories = get_the_terms( $postId, $taxonomy );
			if ( empty( $categories ) ) {
				return false;
			}

			return get_term( $categories[0] );
		}

		return get_term( $primaryCategory );
	}

	/**
	 * @param TermCollection $terms
	 * @param bool $as_links
	 * @return array
	 */
	public static function formatTermsWithParents( TermCollection $terms, bool $as_links = false ): array
	{
		$terms = $terms->all();
		if ( empty( $terms ) ) {
			return [];
		}

		$result = [];
		/** @var Taxonomy $term */
		foreach ( $terms as $term ) {
			try {
				$parent = $term->parent();
			} catch ( Exception $exception ) {
				error_log( __FUNCTION__ . ': ' . $exception->getMessage() );
				continue;
			}

			$parent_name = $parent ? $parent->name : '';
			if ( !isset( $result[ $parent_name ] ) ) {
				$parent_value = $as_links ? [ 'url' => $parent?->link(), 'title' => $parent_name ] : '';
				if ( empty( $parent_value['url'] ) ) {
					$parent_value = $parent_name;
				}
				$result[ $parent_name ] = [
					'parent'   => $parent_value,
					'children' => [],
				];
			}

			$term_name = $term->name;
			$value     = $as_links ? [ 'url' => $term->link(), 'title' => $term_name ] : '';
			if ( empty( $value['url'] ) ) {
				$value = $term_name;
			}
			$result[ $parent_name ]['children'][ $term->name ] = $value;
		}

		foreach ( array_keys( $result ) as $key ) {
			$children = $result[ $key ]['children'] ?? [];
			if ( empty( $children ) ) {
				unset( $result[ $key ] );
				continue;
			}

			asort( $children );
			$result[ $key ]['children'] = $children;
		}

		return $result;
	}

	/**
	 * @param TermCollection $terms
	 * @param bool $as_links
	 * @return array
	 */
	public static function formatTerms( TermCollection $terms, bool $as_links = false ): array
	{
		$terms = $terms->all();
		if ( empty( $terms ) ) {
			return [];
		}

		$result = [];
		/** @var Taxonomy $term */
		foreach ( $terms as $term ) {
			$term_name = $term->name;
			$value     = $as_links ? [ 'url' => $term->link(), 'title' => $term_name ] : '';
			if ( empty( $value['url'] ) ) {
				$value = $term_name;
			}
			$result[ $term_name ] = $value;
		}

		asort( $result );

		return $result;
	}

	/**
	 * @param string $key
	 * @param mixed $postId
	 * @return mixed
	 */
	public static function getMetaValue( string $key, mixed $postId = 0 ): mixed
	{
		if ( !is_numeric( $postId ) ) {
			$object = (object) $postId;
			switch ( true ) {
				case isset( $object->post_type ):
					$postId = $object->ID;
					break;

				case isset( $object->roles ):
					$postId = 'user_' . $object->term_id;
					break;

				case isset( $object->taxonomy ):
					$postId = 'term_' . $object->term_id;
					break;

				default:
					return null;
			}
		} elseif ( (int) $postId <= 0 ) {
			$postId = get_the_ID();
		}

		return get_post_meta( $postId, $key, true );
	}

	/**
	 * @param int|null $postId
	 * @return array
	 */
	public static function listContactPersons( ?int $postId = 0 ): array
	{
		$list = [];
		$key  = 'contact_persons';
		$meta = static::getMetaValue( $key, $postId );
		if ( empty( $meta ) ) {
			return $list;
		}

		foreach ( $meta as $personID ) {
			$post = get_post( $personID );
			if ( empty( $post ) ) {
				continue;
			}

			try {
				$list[] = ContactPerson::fromPost( $post );
			} catch ( Exception $exception ) {
				continue;
			}
		}

		return $list;
	}

}
