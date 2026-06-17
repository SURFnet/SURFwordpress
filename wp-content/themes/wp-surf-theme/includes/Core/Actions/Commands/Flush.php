<?php

namespace SURF\Core\Actions\Commands;

use SURF\Core\Actions\AbstractAction;
use WP_CLI;

/**
 * Class Flush
 * @package SURF\Core\Actions\Commands
 */
class Flush extends AbstractAction
{

	/**
	 * @param array $args
	 * @param array $assocArgs
	 * @return void
	 */
	public function handle( array $args = [], array $assocArgs = [] ): void
	{
		if ( function_exists( 'rocket_clean_domain' ) && function_exists( 'run_rocket_sitemap_preload' ) ) {
			// WP Rocket (automatically does Savvii as well)
			rocket_clean_domain();
			run_rocket_sitemap_preload();

			WP_CLI::success( 'WP Rocket cache flushed successfully.' );
		} elseif ( has_action( 'warpdrive_domain_flush' ) ) {
			// Savvii
			do_action( 'warpdrive_domain_flush' );

			WP_CLI::success( 'Savvii cache flushed successfully.' );
		} else {
			WP_CLI::error( 'No caching plugin found' );
		}
	}

}
