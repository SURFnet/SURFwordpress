<?php

namespace SURF\Providers;

use SURF\Core\Blocks\AcfBlock;
use SURF\Core\Blocks\Block;
use SURF\Core\ClassLoader;
use SURF\Core\Contracts\ServiceProvider;
use SURF\Core\Widgets\Widget;
use SURF\PostTypes\Post;
use WP_Block;
use WP_Block_Editor_Context;
use WP_Block_Type_Registry;

/**
 * Class WidgetServiceProvider
 * @package SURF\Providers
 */
class WidgetServiceProvider extends ServiceProvider
{

	/**
	 * @return void
	 */
	public function register(): void
	{
		// Not implemented
	}

	/**
	 * @return void
	 */
	public function boot(): void
	{
		add_action( 'widgets_init', [ $this, 'registerCustomWidgets' ] );
	}

	/**
	 * @return void
	 */
	public function registerCustomWidgets(): void
	{
		$widgets = ( new ClassLoader() )->loadDirectories(
			array_map( fn( $path ) => $this->app->path( $path ), surfConfig( 'app.paths.widgets' ) )
		);
		foreach ( $widgets as $widget ) {
			if ( !class_exists( $widget ) ) {
				continue;
			}

			if ( is_a( $widget, Widget::class, true ) ) {
				register_widget( $widget );
			}
		}
	}

}
