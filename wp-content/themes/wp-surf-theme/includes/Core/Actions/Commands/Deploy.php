<?php

namespace SURF\Core\Actions\Commands;

use SURF\Core\Actions\AbstractAction;
use WP_CLI;

/**
 * Class Deploy
 * @package SURF\Core\Actions\Commands
 */
class Deploy extends AbstractAction
{

	/**
	 * @param array $args
	 * @param array $assocArgs
	 * @return void
	 */
	public function handle( array $args = [], array $assocArgs = [] ): void
	{
		do_action( 'surf_deploy' );
		WP_CLI::success( "Ran 'surf_deploy' action" );
	}

}
