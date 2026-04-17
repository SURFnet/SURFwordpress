<?php

namespace SURF\Core;

use SURF\Core\Actions\Commands\Deploy;
use SURF\Core\Actions\Commands\Flush;
use SURF\Core\Actions\Commands\SURF;
use WP_CLI;

/**
 * Class CLI
 * @package SURF\Core
 */
class CLI
{

	/**
	 * Init CLI commands.
	 */
	public static function init()
	{
		if ( !defined( 'WP_CLI' ) || !WP_CLI ) {
			return;
		}

		/*
		 * Flush cache (WP Rocket or Savvii).
		 *
		 * Command line usage: `wp surf-flush-cache`
		 */
		WP_CLI::add_command( 'surf-flush-cache', [ Flush::class, 'run' ] );

		/*
		 * Fires a do_action to hook into on every deploy
		 *
		 * Command line usage: `wp surf-deploy`
		 */
		WP_CLI::add_command( 'surf-deploy', [ Deploy::class, 'run' ] );

		/*
		 * Handles multiple commands
		 *
		 * Command line usage:
		 * - `wp surf deploy`
		 * - `wp surf flush`
		 * - `wp surf db:seed`
		 * - `wp surf db:refresh (--seed)`
		 */
		WP_CLI::add_command( 'surf', [ SURF::class, 'run' ] );
	}

}
