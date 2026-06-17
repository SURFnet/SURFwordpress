<?php

namespace SURF\Core\Traits;

use Exception;

/**
 * Trait HasAttributes
 * @package SURF\Core\Traits
 */
trait HasAttributes
{

	protected array $attributes = [];

	/**
	 * @return array
	 */
	public function getAttributes(): array
	{
		return $this->attributes;
	}

	/**
	 * @param string $attribute
	 * @return mixed
	 */
	public function getAttribute( string $attribute ): mixed
	{
		return $this->attributes[ $attribute ] ?? null;
	}

	/**
	 * @param string $attribute
	 * @return bool
	 */
	public function hasAttribute( string $attribute ): bool
	{
		return isset( $this->attributes[ $attribute ] );
	}

	/**
	 * @param string $attribute
	 * @param mixed $value
	 * @return void
	 * @throws Exception
	 */
	public function setAttribute( string $attribute, mixed $value )
	{
		if (
			isset( $this->guarded )
			&& is_array( $this->guarded )
			&& in_array( $attribute, $this->guarded )
		) {
			throw new Exception( "Trying to set read-only attribute '{$attribute}'" );
		}

		$this->attributes[ $attribute ] = $value;
	}

	/**
	 * @param string $attribute
	 * @return mixed
	 */
	public function __get( string $attribute )
	{
		return $this->getAttribute( $attribute );
	}

	/**
	 * @param string $attribute
	 * @param mixed $value
	 * @return void
	 * @throws Exception
	 */
	public function __set( string $attribute, mixed $value )
	{
		$this->setAttribute( $attribute, $value );
	}

	/**
	 * @param mixed $offset
	 * @return bool
	 */
	public function offsetExists( mixed $offset ): bool
	{
		return $this->hasAttribute( $offset );
	}

	/**
	 * @param mixed $offset
	 * @return mixed
	 */
	public function offsetGet( mixed $offset ): mixed
	{
		return $this->getAttribute( $offset );
	}

	/**
	 * @param mixed $offset
	 * @param mixed $value
	 * @return void
	 * @throws Exception
	 */
	public function offsetSet( mixed $offset, mixed $value ): void
	{
		$this->setAttribute( $offset, $value );
	}

	/**
	 * @param mixed $offset
	 * @return void
	 * @throws Exception
	 */
	public function offsetUnset( mixed $offset ): void
	{
		$this->setAttribute( $offset, null );
	}

}
