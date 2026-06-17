<?php

namespace SURF\Core\Traits;

use SURF\Core\PostTypes\BasePost;
use SURF\Core\Taxonomies\Taxonomy;

/**
 * Trait HasFields
 * @package SURF\Core\Traits
 */
trait HasFields
{

	/**
	 * @return string
	 */
	abstract public static function getName(): string;

	/**
	 * @return array
	 */
	abstract public static function getFields(): array;

	/**
	 * @return string
	 */
	public static function getFieldsLocationParam(): string
	{
		$map = [
			Taxonomy::class => 'taxonomy',
			BasePost::class => 'post_type',
		];
		foreach ( $map as $key => $value ) {
			if ( is_a( static::class, $key, true ) ) {
				return $value;
			}
		}

		return 'post_type';
	}

	/**
	 * @return void
	 */
	public static function registerFields(): void
	{
		if ( !function_exists( 'acf_add_local_field_group' ) ) {
			return;
		}

		$groups = static::getFields();
		$groups = count( array_filter( array_keys( $groups ), 'is_string' ) ) > 0
			? [ $groups ]
			: $groups;

		foreach ( $groups as $fields ) {
			if ( empty( $fields ) ) {
				return;
			}

			if ( !isset( $fields['location'] ) ) {
				$fields['location'] = [
					[
						[
							'param'    => static::getFieldsLocationParam(),
							'operator' => '==',
							'value'    => static::getName(),
						],
					],
				];
			}

			acf_add_local_field_group( $fields );
		}
	}

}
