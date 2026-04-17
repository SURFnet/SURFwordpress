<?php

namespace SURF\Traits;

use SURF\Enums\Theme;

/**
 * Trait HasCategoryColor
 * @package SURF\Traits
 */
trait HasCategoryColor
{

	public const FIELD_CATEGORY_COLOR = 'category_color';

	/**
	 * @param string|null $default
	 * @return string|null
	 */
	public function getColor( ?string $default = null ): ?string
	{
		return $this->getMeta( static::FIELD_CATEGORY_COLOR, $default );
	}

	/**
	 * @param $id
	 * @param $default
	 * @return string|null
	 */
	public static function getTermColor( $id, $default = null ): ?string
	{
		$term = static::fromTerm( get_term( $id ) );

		return $term->getColor( $default );
	}

	/**
	 * @return array[]
	 */
	public static function getColorField( string $identifier = 'surf' ): array
	{
		if ( Theme::isPoweredBy() ) {
			return [];
		}

		$choices = array_merge(
			[ '' => _x( 'Default', 'admin', 'wp-surf-theme' ) ],
			Theme::categoryColorPaletteFlat( true )
		);

		return [
			'key'     => 'field_' . $identifier . '_' . static::FIELD_CATEGORY_COLOR,
			'label'   => _x( 'Category color', 'admin', 'wp-surf-theme' ),
			'name'    => static::FIELD_CATEGORY_COLOR,
			'type'    => 'select',
			'choices' => $choices,

		];
	}

}
