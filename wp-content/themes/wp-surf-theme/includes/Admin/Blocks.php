<?php

namespace SURF\Admin;

use Illuminate\Contracts\Container\BindingResolutionException;
use SURF\Core\View\Template;

/**
 * Class Blocks
 * @package SURF\Admin
 */
class Blocks
{

	/**
	 * Register native blocks for Gutenberg.
	 * How to add a new block:
	 * 1. Use the register function here to register the new block
	 * 2. Create a new folder in src/js/gutenberg/block with the name of your block
	 * 3. Create an index.js file inside that new folder and implement the registerBlockType()
	 * 4. If you are building a server side block, create the template file in template-parts/blocks
	 * 5. Re-run [npm run dev] (or [npm run build] before deploying) and upload the assets/js/block contents
	 */
	public static function init() {}

	/**
	 * Register a native Gutenberg block.
	 * @param $block_name
	 * @param bool $server_side
	 * @param array $attributes
	 * @param string $namespace
	 */
	public static function register( $block_name, $server_side = false, array $attributes = [], $namespace = 'surf' )
	{
		$block_arguments = [
			'editor_script' => 'surf.gutenberg',
		];

		if ( $server_side ) {
			$block_arguments['render_callback'] = function ( $arguments, $content ) use ( $block_name )
			{
				return static::renderCallback( $arguments, $content, $block_name );
			};
		}

		if ( $attributes ) {
			$block_arguments['attributes'] = $attributes;
		}

		register_block_type( $namespace . '/' . $block_name, $block_arguments );
	}

	/**
	 * Render callback for server side Gutenberg blocks.
	 * @param $attributes
	 * @param $content
	 * @param string $blockName
	 * @return string
	 */
	public static function renderCallback( $attributes, $content, $blockName = 'example' ): string
	{
		$view = "blocks.{$blockName}";
		try {
			$exists = Template::exists( $view );
		} catch ( BindingResolutionException $exception ) {
			$exists = false;
		}
		if ( !$exists ) {
			return sprintf(
				_x( 'Error: View not found %s', 'admin', 'wp-surf-theme' ),
				"<code>{$view}</code>"
			);
		}

		return Template::render(
			$view,
			[
				'blockAttributes' => $attributes,
				'content'         => $content,
				'blockName'       => $blockName,
			],
			true
		);
	}

}
