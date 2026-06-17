<?php

namespace SURF\Core;

use ArrayAccess;
use Illuminate\Support\Arr;

/**
 * Class Config
 * @package SURF\Core
 */
class Config implements ArrayAccess
{

	protected array $items;

	/**
	 * @param array $items
	 */
	public function __construct( array $items = [] )
	{
		$this->items = $items;
	}

	/**
	 * @param string $key
	 * @param $default
	 * @return array|ArrayAccess|mixed
	 */
	public function get( string $key, $default = null )
	{
		return Arr::get( $this->items, $key, $default );
	}

	/**
	 * @param string $key
	 * @param $value
	 * @return void
	 */
	public function set( string $key, $value )
	{
		Arr::set( $this->items, $key, $value );
	}

	/**
	 * @param string $key
	 * @return bool
	 */
	public function has( string $key ): bool
	{
		return Arr::has( $this->items, $key );
	}

	/**
	 * @param array $paths
	 * @return self
	 */
	public static function load( array $paths ): self
	{
		$items = [];

		foreach ( $paths as $path ) {
			$files = array_filter( scandir( $path ), function ( $file )
			{
				return str_ends_with( $file, '.php' );
			} );

			foreach ( $files as $file ) {
				$key           = str_replace( '.php', '', $file );
				$items[ $key ] = include $path . '/' . $file;
			}
		}

		return new self( $items );
	}

	/**
	 * @param mixed $offset
	 * @return bool
	 */
	public function offsetExists( mixed $offset ): bool
	{
		return $this->has( $offset );
	}

	/**
	 * @param mixed $offset
	 * @return mixed
	 */
	public function offsetGet( mixed $offset ): mixed
	{
		return $this->get( $offset );
	}

	/**
	 * @param mixed $offset
	 * @param mixed $value
	 * @return void
	 */
	public function offsetSet( mixed $offset, mixed $value ): void
	{
		$this->set( $offset, $value );
	}

	/**
	 * @param mixed $offset
	 * @return void
	 */
	public function offsetUnset( mixed $offset ): void
	{
		$this->set( $offset, null );
	}

}
