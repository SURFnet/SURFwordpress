<?php

namespace SURF\Traits;

use SURF\Enums\Theme;

/**
 * Trait HasManagedTaxonomies
 * @package SURF\Traits
 */
trait HasManagedTaxonomies
{

	/**
	 * @return void
	 */
	public static function registerTaxonomySettings()
	{
		add_action( 'pre_get_posts', [ static::class, 'disableTaxonomyArchives' ] );
		add_action( 'acf/init', [ static::class, 'registerTaxonomySettingsPage' ], 11 );
		add_action( 'acf/init', [ static::class, 'registerTaxonomySettingsFields' ], 12 );
		add_action( 'acf/options_page/save', [ static::class, 'afterTaxonomySettingsSave' ], 10, 2 );
	}

	/**
	 * @return string
	 */
	abstract public static function getName(): string;

	/**
	 * @return string
	 */
	public static function getTaxonomySettingsName(): string
	{
		return static::getName() . '_taxonomy_settings';
	}

	/**
	 * @return string
	 */
	public static function getTaxonomyDisableOptionPrefix(): string
	{
		return 'disable_taxonomy_' . static::getName() . '_';
	}

	/**
	 * @param string $tax_name
	 * @return string
	 */
	public static function getTaxonomyDisableOptionName( string $tax_name ): string
	{
		return static::getTaxonomyDisableOptionPrefix() . $tax_name;
	}

	/**
	 * @return void
	 */
	public static function registerTaxonomySettingsPage(): void
	{
		if ( !function_exists( 'acf_add_options_page' ) || count( static::getTaxonomies() ) < 1 ) {
			return;
		}

		acf_add_options_page( [
			'page_title'  => _x( 'Taxonomy Settings', 'admin', 'wp-surf-theme' ),
			'menu_title'  => _x( 'Taxonomy Settings', 'admin', 'wp-surf-theme' ),
			'menu_slug'   => static::getTaxonomySettingsName(),
			'capability'  => 'manage_options',
			'redirect'    => false,
			'parent_slug' => static::getName() === 'post' ? 'edit.php' : 'edit.php?post_type=' . static::getName(),
		] );
	}

	/**
	 * @return void
	 */
	public static function registerTaxonomySettingsFields(): void
	{
		$groups = static::getTaxonomySettingsFields();
		$groups = count( array_filter( array_keys( $groups ), 'is_string' ) ) > 0
			? [ $groups ]
			: $groups;

		foreach ( $groups as $fields ) {
			if ( empty( $fields ) ) {
				continue;
			}

			if ( !isset( $fields['location'] ) ) {
				$fields['location'] = [
					[
						[
							'param'    => 'options_page',
							'operator' => '==',
							'value'    => static::getTaxonomySettingsName(),
						],
					],
				];
			}

			acf_add_local_field_group( $fields );
		}
	}

	/**
	 * Flushes rewrite rules if any of the taxonomy archive disable options were changed
	 * @param int|string $post_id
	 * @param string $menu_slug
	 * @return void
	 */
	public static function afterTaxonomySettingsSave( int|string $post_id, string $menu_slug ): void
	{
		if ( static::getTaxonomySettingsName() !== $menu_slug ) {
			return;
		}

		$post_data = $_POST['acf'] ?? [];
		if ( empty( $post_data ) ) {
			return;
		}

		$post_values = [];
		$offset      = strlen( 'field_' );
		foreach ( $post_data as $key => $value ) {
			// Strip the 'field_' prefix from the ACF field key to get the actual option name
			$option_name = substr( $key, $offset );
			if ( !str_starts_with( $option_name, static::getTaxonomyDisableOptionPrefix() ) ) {
				continue;
			}

			$post_values[ $option_name ] = $value;
		}
		if ( empty( $post_values ) ) {
			return;
		}

		// Compare the submitted values with the current values in the database
		foreach ( $post_values as $option_name => $option_value ) {
			$db_value = surfGetGlobalThemeOption( $option_name );
			if ( $db_value !== $option_value ) {
				flush_rewrite_rules( true );
				break;
			}
		}
	}

	/**
	 * @return array
	 */
	public static function getTaxonomySettingsFields(): array
	{
		$taxonomies = static::getTaxonomies();

		return [
			'key'    => static::getName() . '_group_taxonomy_settings',
			'title'  => _x( 'Taxonomy Settings', 'admin', 'wp-surf-theme' ),
			'fields' => [
				...array_values( array_map( function ( $taxonomy )
				{
					$field_name = static::getTaxonomyDisableOptionName( $taxonomy->name );

					return [
						'key'   => 'field_' . $field_name,
						'name'  => $field_name,
						'label' => sprintf( _x( 'Disable %s taxonomy archive', 'admin', 'wp-surf-theme' ), $taxonomy->label ),
						'type'  => 'true_false',
						'ui'    => true,
						'ui_on_text'  => _x( 'Archive disabled', 'admin', 'wp-surf-theme' ),
						'ui_off_text' => _x( 'Archive enabled', 'admin', 'wp-surf-theme' ),
					];
				}, $taxonomies ) ),
			],
		];
	}

	/**
	 * @return array
	 */
	public static function getTaxonomies(): array
	{
		$taxonomies = get_object_taxonomies( static::getName(), 'objects' );

		return array_filter( $taxonomies, function ( $taxonomy )
		{
			return $taxonomy->public && $taxonomy->publicly_queryable;
		} );
	}

	/**
	 * @param $query
	 * @return void
	 */
	public static function disableTaxonomyArchives( $query )
	{
		if ( !is_tax() ) {
			return;
		}

		$taxonomies = static::getTaxonomies();
		foreach ( $taxonomies as $taxonomy ) {
			$tax_name = $taxonomy->name;
			if ( !is_tax( $tax_name ) ) {
				continue;
			}

			if ( !static::hasTaxArchive( $tax_name ) ) {
				$query->set_404();
			}
		}
	}

	/**
	 * @param string $tax_name
	 * @return bool
	 */
	public static function hasTaxArchive( string $tax_name ): bool
	{
		$option_name = static::getTaxonomyDisableOptionName( $tax_name );

		return !Theme::getGlobalOption( $option_name );
	}

}
