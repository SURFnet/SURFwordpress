<?php

namespace SURF\Core\Traits;

/**
 * Trait HasMeta
 * @package SURF\Core\Traits
 */
trait HasMeta
{
	public const MAX_REPEATER_META_ROWS = 100;

	/**
	 * @return string
	 */
	public abstract function getMetaType(): string;

	/**
	 * @return int
	 */
	public abstract function getMetaId(): int;

	/**
	 * @return array
	 */
	public function getAllMeta(): array
	{
		return array_map( function ( $values )
		{
			return array_shift( $values );
		}, get_metadata( $this->getMetaType(), $this->getMetaId() ) );
	}

	/**
	 * @param string $key
	 * @param mixed $default
	 * @return mixed
	 */
	public function getMeta( string $key, mixed $default = null ): mixed
	{
		if ( empty( $key ) ) {
			return $default;
		}

		if ( !metadata_exists( $this->getMetaType(), $this->getMetaId(), $key ) ) {
			return $default;
		}

		return get_metadata( $this->getMetaType(), $this->getMetaId(), $key, true );
	}

	/**
	 * @param string $key
	 * @param array $subFields
	 * @return mixed
	 */
	public function getRepeaterMeta( string $key, array $subFields ): array
	{
		$list  = [];
		$count = $this->getMeta( $key, 0 );
		if ( empty( $count ) || !is_numeric( $count ) ) {
			return $list;
		}

		$count = min( (int) $count, static::MAX_REPEATER_META_ROWS );
		for ( $i = 0; $i < $count; $i++ ) {
			$values = [];
			foreach ( $subFields as $subKey => $subDefault ) {
				if ( is_int( $subKey ) ) {
					$subKey     = $subDefault;
					$subDefault = null;
				}
				$values[ $subKey ] = $this->getMeta( $key . '_' . $i . '_' . $subKey, $subDefault );
			}
			$list[ $i ] = $values;
		}

		return $list;
	}

	/**
	 * @param string $key
	 * @param mixed $value
	 * @return void
	 */
	public function updateMeta( string $key, mixed $value ): void
	{
		update_metadata( $this->getMetaType(), $this->getMetaId(), $key, $value );
	}

	/**
	 * @param string $key
	 * @return void
	 */
	public function deleteMeta( string $key ): void
	{
		delete_metadata( $this->getMetaType(), $this->getMetaId(), $key );
	}

}
