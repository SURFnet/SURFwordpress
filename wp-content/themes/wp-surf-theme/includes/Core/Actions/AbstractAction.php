<?php

namespace SURF\Core\Actions;

/**
 * Class AbstractAction
 * @package SURF\Core\Actions
 */
abstract class AbstractAction
{

	/**
	 * @param ...$args
	 * @return void
	 */
	public static function run( ...$args )
	{
		if ( method_exists( static::class, 'handle' ) ) {
			( new static )->handle( ...$args );
		}
	}

	/**
	 * @param ...$args
	 * @return void
	 */
	public function __invoke( ...$args )
	{
		if ( method_exists( $this, 'handle' ) ) {
			$this->handle( ...$args );
		}
	}

}
