<?php

namespace SURF\Core\Actions\Commands\DB;

use Exception;
use SURF\DB\Seeders\DefaultSeeder;
use SURF\Core\Actions\AbstractAction;
use SURF\Core\DB\AbstractSeeder;
use WP_CLI;

/**
 * Class Seed
 * Command to seed the database with initial data
 * @package SURF\Core\Actions\Commands\DB
 */
class Seed extends AbstractAction
{

	/**
	 * @param array $args
	 * @param array $assocArgs
	 * @return void
	 * @throws Exception
	 */
	public function handle( array $args = [], array $assocArgs = [] ): void
	{
		$class = $args[0] ?? $assocArgs['class'] ?? DefaultSeeder::class;

		if ( class_exists( $class ) && is_a( $class, AbstractSeeder::class, true ) ) {
			( new $class )->run();
		}

		WP_CLI::success( 'Database was seeded.' );
	}

}
