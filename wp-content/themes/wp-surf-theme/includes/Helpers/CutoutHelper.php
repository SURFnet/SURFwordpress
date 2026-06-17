<?php

namespace SURF\Helpers;

/**
 * Class CutoutHelper
 * @package SURF\Helpers
 */
class CutoutHelper
{

	/**
	 * @param $element
	 * @param $corner
	 * @param $label
	 * @return array
	 */
	public static function getCornerSettings( $element = '', $corner = '', $label = '' )
	{
		return [
			'key'        => 'field_theme_settings_ib_' . $element . '_cutout_' . $corner,
			'label'      => $label,
			'name'       => $corner,
			'type'       => 'group',
			'wrapper'    => [ 'width' => 50 ],
			'sub_fields' => [
				[
					'key'    => 'field_theme_settings_ib_' . $element . '_cutout_' . $corner . '_x',
					'label'  => _x( 'X axis', 'admin', 'wp-surf-theme' ),
					'name'   => 'x',
					'type'   => 'range',
					'min'    => 0,
					'max'    => 100,
					'step'   => 1,
					'append' => '%',
				],
				[
					'key'    => 'field_theme_settings_ib_' . $element . '_cutout_' . $corner . '_y',
					'label'  => _x( 'Y axis', 'admin', 'wp-surf-theme' ),
					'name'   => 'y',
					'type'   => 'range',
					'min'    => 0,
					'max'    => 100,
					'step'   => 1,
					'append' => '%',
				],
			],
		];
	}

}
