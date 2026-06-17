<?php

namespace SURF\Helpers;

use Symfony\Component\VarDumper\VarDumper;

/**
 * Class DebugHelper
 * @package SURF\Helpers
 */
class DebugHelper
{

	/**
	 * Dump pretty data.
	 * @param ...$values
	 */
	public static function dump( ...$values )
	{
		foreach ( $values as $v ) {
			VarDumper::dump( $v );
		}
	}

	/**
	 * Run a benchmark with one or more callback functions
	 * @param int $times             - The amount of times to run the callback functions
	 * @param callable ...$callbacks - The callback functions to benchmark
	 * @return array - An array with the total execution time for each callback
	 */
	public static function benchmark( int $times, callable ...$callbacks ): array
	{
		$results = [];
		foreach ( $callbacks as $cb ) {
			$start = microtime( true );
			for ( $i = 0; $i < $times; $i++ ) {
				$cb();
			}
			$results[] = microtime( true ) - $start;
		}

		return $results;
	}

}
