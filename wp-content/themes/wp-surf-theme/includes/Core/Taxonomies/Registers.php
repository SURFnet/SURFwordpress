<?php

namespace SURF\Core\Taxonomies;

use Illuminate\Support\Str;
use ReflectionClass;
use SURF\Core\Exceptions\MismatchingPostTypesException;
use SURF\Helpers\PolylangHelper;
use SURF\PostTypes\Agenda;
use SURF\PostTypes\Page;

/**
 * Trait Registers
 * Provides functionality to register custom taxonomies in WordPress
 * @package SURF\Core\Taxonomies
 */
trait Registers
{

	/**
	 * @return array
	 */
	abstract public static function getPostTypes(): array;

	/**
	 * @return string
	 */
	abstract public static function getName(): string;

	/**
	 * @return void
	 */
	public static function registered(): void {}

	/**
	 * @return void
	 * @throws MismatchingPostTypesException
	 */
	public static function register()
	{
		$args = [
			'labels'             => static::getLabels(),
			'description'        => '',
			'public'             => true,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'show_in_nav_menus'  => true,
			'show_in_rest'       => true,
			'show_admin_column'  => true,
			'rest_base'          => static::getName(),
			'rewrite'            => [ 'slug' => static::getRewriteSlug(), 'with_front' => true ],
			'hierarchical'       => true,
			'query_var'          => true,
		];

		register_taxonomy(
			static::getName(),
			static::getPostTypes(),
			array_merge( $args, static::getArgs() )
		);

		static::registerRewriteRules();
		static::registered();
	}

	/**
	 * @return void
	 * @throws MismatchingPostTypesException
	 */
	public static function registerRewriteRules(): void
	{
		add_rewrite_rule(
			'^' . static::getRewriteSlug() . '/([^/]+)/?$',
			'index.php?' . static::getName() . '=$matches[1]',
			'top'
		);
	}

	/**
	 * @return array
	 */
	public static function getArgs(): array
	{
		return [];
	}

	/**
	 * @return string
	 */
	public static function getSingularLabel(): string
	{
		return static::getClassName();
	}

	/**
	 * @return string
	 */
	public static function getPluralLabel(): string
	{
		return Str::plural( static::getClassName() );
	}

	/**
	 * @return string
	 * @throws MismatchingPostTypesException
	 */
	public static function getRewriteSlug(): string
	{
		if ( count( static::$postTypes ) > 1 ) {
			return static::getSlug();
		}

		$postType = static::$postTypes[0];

		if ( !method_exists( $postType, 'getArchivePageOption' ) ) {
			return static::getSlug();
		}

		$option = $postType::getArchivePageOption();
		$lang   = PolylangHelper::getCurrentLanguageSlug();
		$page   = Page::findForLanguage( get_option( $option ), $lang );

		$slug = $page ? $page->post_name : $postType::getSlug();

		return $slug . '/' . static::getSlug();
	}

	/**
	 * @return string
	 */
	public static function getSlug(): string
	{
		return Str::slug( static::getClassName() );
	}

	/**
	 * @return string
	 */
	public static function getClassName(): string
	{
		return ( new ReflectionClass( static::class ) )->getShortName();
	}

	/**
	 * @return array
	 */
	public static function getLabels(): array
	{
		$singular = static::getSingularLabel();
		$plural   = static::getPluralLabel();

		return [
			'name'                => sprintf( _x( '%s', 'post type general name', 'wp-surf-theme' ), $plural ),
			'singular_name'       => sprintf( _x( '%s', 'post type singular name', 'wp-surf-theme' ), $singular ),
			'add_new'             => sprintf( __( 'Add %s', 'wp-surf-theme' ), $singular ),
			'search_items'        => sprintf( __( 'Search %s', 'wp-surf-theme' ), $plural ),
			'popular_items'       => sprintf( __( 'Popular %s', 'wp-surf-theme' ), $plural ),
			'all_items'           => sprintf( __( 'All %s', 'wp-surf-theme' ), $plural ),
			'parent_items'        => sprintf( __( 'Parent %s', 'wp-surf-theme' ), $singular ),
			'parent_item_colon'   => sprintf( __( 'Parent %s:', 'wp-surf-theme' ), $plural ),
			'edit_item'           => sprintf( __( 'Edit %s', 'wp-surf-theme' ), $singular ),
			'view_item'           => sprintf( __( 'View %s', 'wp-surf-theme' ), $singular ),
			'update_item'         => sprintf( __( 'Update %s', 'wp-surf-theme' ), $singular ),
			'add_new_item'        => sprintf( __( 'Add New %s', 'wp-surf-theme' ), $singular ),
			'new_item_name'       => sprintf( __( 'New %s Name', 'wp-surf-theme' ), $singular ),
			'add_or_remove_items' => sprintf( __( 'Add or remove %s', 'wp-surf-theme' ), $plural ),
			'not_found'           => sprintf( __( 'No %s found.', 'wp-surf-theme' ), $plural ),
			'no_terms'            => sprintf( __( 'No %s', 'wp-surf-theme' ), $plural ),
		];
	}

}
