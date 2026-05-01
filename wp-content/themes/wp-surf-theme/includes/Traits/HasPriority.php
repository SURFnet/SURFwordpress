<?php

namespace SURF\Traits;

use Illuminate\Support\Collection;
use SURF\Core\PostTypes\BasePost;

/**
 * Trait HasPriority
 * @package SURF\Traits
 */
trait HasPriority
{

	public const FIELD_PRIORITY = 'priority';

	/**
	 * @return int|null
	 */
	public function getPriority(): ?int
	{
		$value = $this->getMeta( static::FIELD_PRIORITY, null );
		return $value ? (int) $value : null;
	}

	/**
	 * @param array $args
	 * @param string $fallback_property
	 * @return Collection
	 */
	public static function querySortedByPriority( array $args = [], string $fallback_property = 'name' ): Collection
	{
		/** @var Collection $query */
		$query = static::query( $args );

		return $query->sort( function ( self $a, self $b ) use ( $fallback_property )
		{
			if ( $a->getPriority() && $b->getPriority() ) {
				return $a->getPriority() > $b->getPriority() ? 1 : -1;
			}

			if ( $a->getPriority() ) {
				return -1;
			}

			if ( $b->getPriority() ) {
				return 1;
			}

			if ( !property_exists( $a, $fallback_property ) || !property_exists( $b, $fallback_property ) || in_array($fallback_property, [ 'name', 'post_name' ] ) ) {
				$a_name = $a->name;
				$b_name = $b->name;
				if ( is_a( $a, BasePost::class, true ) ) {
					$a_name = $a->post_name;
				}
				if ( is_a( $b, BasePost::class, true ) ) {
					$b_name = $b->post_name;
				}

				return strcmp( strtolower( $a_name ), strtolower( $b_name ) );
			}

			$a_value = $a->{$fallback_property};
			$b_value = $b->{$fallback_property};
			if ( is_numeric( $a_value ) && is_numeric( $b_value ) ) {
				return $a_value > $b_value ? 1 : -1;
			}

			return strcmp( strtolower( $a_value ), strtolower( $b_value ) );
		} );
	}

	/**
	 * @return array[]
	 */
	public static function getPriorityField( string $identifier = 'surf' ): array
	{
		return [
			'key'   => 'field_' . $identifier . '_' . static::FIELD_PRIORITY,
			'label' => _x( 'Priority', 'admin', 'wp-surf-theme' ),
			'name'  => static::FIELD_PRIORITY,
			'type'  => 'number',
			'min'   => 1,
		];
	}

}
