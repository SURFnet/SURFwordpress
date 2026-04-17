<?php

namespace SURF\Admin;

use SURF\Core\View\Template;

/**
 * Class BlockPatterns
 * @package SURF\Admin
 */
class BlockPatterns
{

	/**
	 * @return void
	 */
	public static function init(): void
	{
		// Disable core patterns
		remove_theme_support( 'core-block-patterns' );

		// Categories
		static::register_category( 'surf-template-parts', _x( 'Template parts', 'Block pattern category', 'wp-surf-theme' ) );

		// Patterns
		static::register_pattern( 'agenda-columns', [
			'title'       => _x( 'Agenda - Columns', 'Block pattern title', 'wp-surf-theme' ),
			'description' => _x( 'A pattern that setups the agenda columns block', 'Block pattern description', 'wp-surf-theme' ),
			'categories'  => [ 'surf-template-parts' ],
		], true );
	}

	/**
	 * Init block pattern register
	 * @param string $slug
	 * @param array $data
	 * @param bool $content_part
	 * @return void
	 */
	public static function register_pattern( $slug = '', $data = [], $content_part = false ): void
	{
		if ( !$slug || empty( $data ) || !function_exists( 'register_block_pattern' ) ) {
			return;
		}

		// Get content part from template parts
		if ( $content_part ) {
			$data['content'] = Template::render(
				"patterns.{$slug}",
				compact( 'slug', 'data' ),
				true
			);
		}

		register_block_pattern( "surf/{$slug}", $data );
	}

	/**
	 * Init pattern category register
	 * @param string $slug
	 * @param string $name
	 * @return void
	 */
	public static function register_category( $slug = '', $name = '' ): void
	{
		if ( !$slug || !$name || !function_exists( 'register_block_pattern_category' ) ) {
			return;
		}
		register_block_pattern_category( $slug, [ 'label' => $name ] );
	}

}
