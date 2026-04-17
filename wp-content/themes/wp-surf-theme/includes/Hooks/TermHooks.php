<?php

namespace SURF\Hooks;

/**
 * Class TermHooks
 * @package SURF\Hooks
 */
class TermHooks
{

	/**
	 * Register Term hooks
	 */
	public static function register()
	{
		static::allowHTMLDescription();
	}

	/**
	 * Get taxonomies that should allow HTML in their descriptions
	 * @return array
	 */
	public static function allowedWithHTMLDescription(): array
	{
		return apply_filters( 'surf_taxonomy_html_description', [] );
	}

	/**
	 * Allow HTML in taxonomy descriptions for specific taxonomies
	 * @return void
	 */
	public static function allowHTMLDescription(): void
	{
		static::updateDescriptionSaveFilter();
		static::updateDescriptionDisplayFilters();
	}

	/**
	 * Updates the save filter for the description field to allow HTML for specific taxonomies
	 * @return void
	 */
	public static function updateDescriptionSaveFilter(): void
	{
		$hook_name = 'pre_term_description';
		remove_filter( $hook_name, 'wp_filter_kses' );
		add_filter( $hook_name, function ( mixed $value, string $taxonomy )
		{
			$allowed = static::allowedWithHTMLDescription();
			if ( in_array( $taxonomy, $allowed ) ) {
				return wp_kses( $value, wp_kses_allowed_html( 'post' ) );
			}

			return wp_filter_kses( $value );
		}, 9, 2 );
	}

	/**
	 * Updates the display filters for the description field to allow HTML for specific taxonomies
	 * @return void
	 */
	public static function updateDescriptionDisplayFilters(): void
	{
		$hook_name = 'term_description';
		remove_filter( $hook_name, 'wp_kses_data' );
		add_filter( $hook_name, function ( mixed $value, int $term_id, string $taxonomy, string $context )
		{
			$allowed = static::allowedWithHTMLDescription();
			if ( in_array( $taxonomy, $allowed ) ) {
				return $value;
			}

			return wp_kses_data( $value );
		}, 9, 4 );
	}

}
