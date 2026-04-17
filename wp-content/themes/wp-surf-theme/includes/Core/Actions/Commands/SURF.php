<?php

namespace SURF\Core\Actions\Commands;

use SURF\Core\Actions\AbstractAction;
use SURF\Core\Actions\Commands\DB\Refresh;
use SURF\Core\Actions\Commands\DB\Seed;

/**
 * Class SURF
 * Main command handler for SURF commands
 * @package SURF\Core\Actions\Commands
 */
class SURF extends AbstractAction
{

	protected $commands = [
		'deploy' => Deploy::class,
		'flush'  => Flush::class,
		'db'     => [
			'seed'    => Seed::class,
			'refresh' => Refresh::class,
		],
	];

	/**
	 * @param array $args
	 * @param array $assocArgs
	 * @return void
	 */
	public function handle( array $args = [], array $assocArgs = [] ): void
	{
		$command = array_shift( $args );
		$parts   = explode( ':', $command );

		// Retrieve the correct item from the $commands array
		// Example: db:seed will retrieve $this->commands['db']['seed]
		$callable = array_reduce( $parts, function ( $carry, $item )
		{
			return $carry[ $item ] ?? null;
		}, $this->commands );

		if ( is_string( $callable ) && method_exists( $callable, '__invoke' ) ) {
			$callable = new $callable;
		}

		if ( is_callable( $callable ) ) {
			$callable( $args, $assocArgs );
		}
	}

}
