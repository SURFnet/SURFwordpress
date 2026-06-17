<?php

namespace SURF\Providers;

use SURF\Core\Blocks\AcfBlock;
use SURF\Core\Blocks\Block;
use SURF\Core\ClassLoader;
use SURF\Core\Contracts\ServiceProvider;
use WP_Block;
use WP_Block_Editor_Context;
use WP_Block_Type_Registry;

/**
 * Class BlockServiceProvider
 * @package SURF\Providers
 */
class BlockServiceProvider extends ServiceProvider
{

	public const ALLOWED_BLOCKS = [
		'core/columns',
		'core/column',
		'core/paragraph',
		'core/media-text',
		'core/buttons',
		'core/button',
		'core/file',
		'core/quote',
		'core/gallery',
		'core/video',
		'core/heading',
		'core/image',
		'core/list',
		'core/list-item',
		'core/shortcode',
		'core/block',
		'core/html',
		'core/embed',
		'core/table',
		'core/navigation',
		'core/navigation-submenu',
		'core/navigation-link',
		'core/home-link',
		'gravityforms/form',
	];
	/**
	 * @var array
	 */
	protected array $blocks;

	/**
	 * @return void
	 */
	public function register()
	{
		$this->disableCoreBlocks();
		$this->registerBlockStyles();

		add_filter( 'render_block', [ $this, 'addYoutubeConsent' ], 10, 3 );
	}

	/**
	 * @return void
	 */
	public function boot()
	{
		$this->registerCustomBlocks();
	}

	/**
	 * @return void
	 */
	public function disableCoreBlocks()
	{
		add_filter(
			'allowed_block_types_all',
			function ( bool|array $allowedBlockTypes, WP_Block_Editor_Context $blockEditorContext )
			{
				if ( $allowedBlockTypes === false ) {
					return false;
				}

				if ( $allowedBlockTypes === true ) {
					$allowedBlockTypes = array_column(
						WP_Block_Type_Registry::get_instance()->get_all_registered(),
						'name'
					);
				}

				return array_values( array_filter( $allowedBlockTypes, function ( $blockType )
				{
					return str_starts_with( $blockType, 'surf/' ) || in_array( $blockType, static::ALLOWED_BLOCKS );
				} ) );
			},
			10,
			2
		);
	}

	/**
	 * @return void
	 */
	public function registerCustomBlocks()
	{
		$blocks = ( new ClassLoader() )->loadDirectories(
			array_map( fn( $path ) => $this->app->path( $path ), surfConfig( 'app.paths.blocks' ) )
		);

		foreach ( $blocks as $block ) {
			if ( !class_exists( $block ) ) {
				continue;
			}

			if (
				is_a( $block, Block::class, true ) ||
				is_a( $block, AcfBlock::class, true )
			) {
				$instance = new $block();
				$instance->register();
			}
		}
	}

	/**
	 * @return void
	 */
	public function registerBlockStyles()
	{
		register_block_style( 'core/button', [
			'name'       => 'primary',
			'label'      => _x( 'Primary', 'admin', 'wp-surf-theme' ),
			'is_default' => true,
		] );

		register_block_style( 'core/button', [
			'name'  => 'secondary',
			'label' => _x( 'Secondary', 'admin', 'wp-surf-theme' ),
		] );

		register_block_style( 'core/paragraph', [
			'name'  => 'lead',
			'label' => _x( 'Larger', 'admin', 'wp-surf-theme' ),
		] );

		register_block_style( 'core/list', [
			'name'  => 'lead',
			'label' => _x( 'Larger', 'admin', 'wp-surf-theme' ),
		] );

		register_block_style( 'core/columns', [
			'name'  => 'extra-wide',
			'label' => _x( 'Extra wide', 'admin', 'wp-surf-theme' ),
		] );

		register_block_style( 'surf/cta', [
			'name'  => 'extra-wide',
			'label' => _x( 'Extra wide', 'admin', 'wp-surf-theme' ),
		] );

		register_block_style( 'surf/roadmap', [
			'name'  => 'extra-wide',
			'label' => _x( 'Extra wide', 'admin', 'wp-surf-theme' ),
		] );

		register_block_style( 'surf/header', [
			'name'  => 'extra-wide',
			'label' => _x( 'Extra wide', 'admin', 'wp-surf-theme' ),
		] );
	}

	/**
	 * @param string $content
	 * @param array $blockData
	 * @param WP_Block $block
	 * @return string
	 */
	public function addYoutubeConsent( string $content, array $blockData, WP_Block $block ): string
	{
		if ( $block->name !== 'core/embed' || !str_contains( $content, 'youtube.com' ) ) {
			return $content;
		}

		$id = 'youtube-consent-wrapper-' . uniqid();

		return surfView( 'consent.youtube', compact( 'id', 'content', 'block' ) );
	}

}
