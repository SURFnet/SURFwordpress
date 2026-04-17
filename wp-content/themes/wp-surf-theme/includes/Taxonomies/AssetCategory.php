<?php

namespace SURF\Taxonomies;

use SURF\Core\Exceptions\MismatchingTaxonomyException;
use SURF\Core\Taxonomies\Registers;
use SURF\Core\Taxonomies\Taxonomy;
use SURF\Core\Traits\HasFields;
use SURF\PostTypes\Asset;
use SURF\Traits\HasPriority;
use WP_Term;

/**
 * Class AssetCategory
 * @package SURF\Taxonomies
 */
class AssetCategory extends Taxonomy
{

	use Registers, HasFields, HasPriority;

	protected static string $taxonomy  = 'surf-asset-category';
	protected static array  $postTypes = [ Asset::class ];

	/**
	 * @return string
	 */
	public static function getSingularLabel(): string
	{
		return _x( 'Category', 'label singular - asset-category', 'wp-surf-theme' );
	}

	/**
	 * @return string
	 */
	public static function getPluralLabel(): string
	{
		return _x( 'Categories', 'label plural - asset-category', 'wp-surf-theme' );
	}

	/**
	 * @return string
	 */
	public static function getSlug(): string
	{
		return _x( 'category', 'tax slug - asset-category', 'wp-surf-theme' );
	}

	/**
	 * @return bool
	 */
	public static function useSlugInFilters(): bool
	{
		return true;
	}

	/**
	 * @return bool
	 */
	public static function shouldShowParents(): bool
	{
		return (bool) get_option( 'options_asset_show_parent_categories', false );
	}

	/**
	 * @return bool
	 */
	public static function hasArchive(): bool
	{
		return Asset::hasTaxArchive( static::getName() );
	}

	/**
	 * @return string
	 */
	public function overviewLink(): string
	{
		$default = $this->link();
		if ( $this->parent !== 0 ) {
			return $default;
		}

		$page = Asset::getAssetsPage();
		if ( empty( $page ) ) {
			return $default;
		}

		return add_query_arg( [ 'category' => $this->slug ], get_permalink( $page->ID ) );
	}

	/**
	 * @return void
	 */
	public static function registered(): void
	{
		add_filter( 'surf_taxonomy_html_description', function ( array $allowed = [] ): array
		{
			return array_unique( array_values( array_merge( $allowed, [ static::getName() ] ) ) );
		} );
	}

	/**
	 * @return array[]
	 */
	public static function getFields(): array
	{
		$identifier = str_replace( '-', '_', static::getName() );

		return [
			[
				'key'    => 'group_' . $identifier . '_settings',
				'title'  => _x( 'Category settings', 'admin', 'wp-surf-theme' ),
				'fields' => [
					static::getPriorityField( $identifier ),
				],
			],
		];
	}

	/**
	 * @param int $term_id
	 * @param int $depth
	 * @return array
	 */
	public static function listChildMenuItems( int $term_id = 0, int $depth = -1 ): array
	{
		$result = [];
		$terms  = static::querySortedByPriority( [ 'parent' => $term_id ], 'term_order' );
		if ( $terms->isEmpty() ) {
			return $result;
		}

		foreach ( $terms as $term ) {
			$term_url = $term->link();
			if ( empty( $term_url ) ) {
				continue;
			}

			$term_id              = $term->term_id;
			$term_slug            = $term->slug;
			$result[ $term_slug ] = [
				'id'      => $term_id,
				'slug'    => $term_slug,
				'title'   => $term->name,
				'url'     => $term_url,
				'classes' => [],
			];

			if ( $depth === 0 ) {
				continue;
			}

			$depth    = $depth > 0 ? $depth - 1 : $depth;
			$children = static::listChildMenuItems( $term_id, $depth );
			if ( empty( $children ) ) {
				continue;
			}

			$result[ $term_slug ]['children'] = $children;
		}

		return $result;
	}

	/**
	 * Gets top-level menu items
	 * @param AssetCategory|null $current
	 * @return array
	 */
	public static function getTopMenu( ?AssetCategory $current = null, string $item_class = 'top-menu__item' ): array
	{
		$items = [];
		$terms = static::querySortedByPriority( [ 'parent' => 0 ], 'term_order' );
		if ( $terms->isEmpty() ) {
			return $items;
		}

		// Allow for adding an active state to the top-level ancestor of the current term, if applicable
		$active_id = null;
		if ( $current instanceof Taxonomy ) {
			$active_id = static::getTopAncestorId( $current );
		}

		// Loop terms and build menu items, only including those with children
		foreach ( $terms as $term ) {
			$term_id   = $term->term_id;
			$term_slug = $term->slug;
			$menu_item = [
				'id'       => $term_id,
				'slug'     => $term_slug,
				'title'    => $term->name,
				'url'      => $term->link(),
				'selected' => $active_id && $term->term_id === $active_id,
				'classes'  => [ $item_class ],
				'children' => static::listChildMenuItems( $term_id, 2 ),
			];
			if ( empty( $menu_item['children'] ) ) {
				continue;
			}

			if ( $menu_item['selected'] ) {
				$menu_item['classes'][] = $item_class . '--active';
			}
			$items[] = $menu_item;
		}

		return $items;
	}

	/**
	 * Gets the top ancestor ID for a given term
	 * @param AssetCategory $term
	 * @return int
	 */
	public static function getTopAncestorId( AssetCategory $term ): int
	{
		$parent_id = $term->parent;
		if ( $parent_id === 0 ) {
			return $term->term_id;
		}

		try {
			$parent = static::find( $parent_id );
			if ( $parent instanceof Taxonomy ) {
				return static::getTopAncestorId( $parent );
			}
		} catch ( MismatchingTaxonomyException $exception ) {
			// If we can't find the parent, just return the current term ID
		}

		return $term->term_id;
	}

	/**
	 * Gets submenu items for a given term
	 * Shows children if they exist, otherwise siblings
	 * @param null|AssetCategory $current
	 * @return array
	 * @throws MismatchingTaxonomyException
	 */
	public static function getAsideMenu( ?AssetCategory $current = null ): array
	{
		$items       = [];
		$back_term   = false;
		$term_list   = collect();
		$current_id  = $current->term_id ?? 0;
		$selected_id = $current_id;
		$heading     = __( 'Categories', 'wp-surf-theme' );

		$selected_slug = surfGetGet( 'category' );
		if ( empty( $current ) || $selected_slug !== $current->slug ) {
			$selected_term = get_term_by( 'slug', $selected_slug, static::getName() );
			if ( $selected_term instanceof WP_Term ) {
				$selected_id = $selected_term->term_id;
			}
		}

		// If term has children, show children
		$children = static::querySortedByPriority( [ 'parent' => $current_id ], 'term_order' );
		if ( $children->isNotEmpty() ) {
			$back_term = $current;
			$term_list = $children;

		} else {
			try {
				$parent_id = $current->parent;
				$parent    = $parent_id ? static::find( $parent_id ) : null;
				if ( $parent instanceof Taxonomy ) {
					$back_term = $parent;
					$term_list = static::querySortedByPriority( [ 'parent' => $parent_id ], 'term_order' );
				}
			} catch ( MismatchingTaxonomyException $exception ) {
				// If we can't find the parent, just show an empty list
			}
		}

		// Add back link if we have a parent
		if ( $back_term instanceof Taxonomy ) {
			$item_link = Asset::getArchiveLink();
			$item_text = __( 'Back to main categories', 'wp-surf-theme' );

			$parent_id = $back_term->parent;
			if ( !empty( $parent_id ) ) {
				$item_link = '';

				$parent_term = static::find( $parent_id );
				if ( $parent_term instanceof Taxonomy ) {
					$item_link = $parent_term->link();
					$item_text = sprintf( __( 'Back to %1$s', 'wp-surf-theme' ), $parent_term->name );
				}
			}

			if ( !empty( $item_link ) ) {
				$heading   = $back_term->name;
				$back_link = [
					'id'        => $back_term->term_id,
					'slug'      => $back_term->slug,
					'title'     => $item_text,
					'url'       => $item_link,
					'back_link' => true,
					'selected'  => $back_term->term_id === $selected_id,
				];
			}
		}

		// Loop terms and build menu items
		foreach ( $term_list as $term ) {
			$items[] = [
				'id'       => $term->term_id,
				'slug'     => $term->slug,
				'title'    => $term->name,
				'url'      => $term->link(),
				'selected' => $term->term_id === $selected_id,
			];
		}

		if ( !empty( $back_link ) ) {
			$items[] = $back_link;
		}

		if (empty($items)) {
			return [];
		}

		return [
			'heading' => $heading,
			'items'   => $items,
		];
	}

}
