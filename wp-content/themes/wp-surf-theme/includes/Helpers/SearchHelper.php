<?php

namespace SURF\Helpers;

/**
 * Class SearchHelper
 * @package SURF\Helpers
 */
class SearchHelper
{

	/**
	 * @param array $args
	 * @param string|null $placeholder
	 * @return string
	 */
	public static function getForm( array $args = [], ?string $placeholder = null ): string
	{
		$args['echo'] = false;
		if ( empty( $placeholder ) ) {
			$placeholder = _x( 'Search...', 'placeholder', 'wp-surf-theme' );
		}

		$prefix  = 'placeholder="';
		$replace = $prefix . esc_attr( $placeholder ) . '"';
		$search  = [
			$prefix . 'Zoeken"',
			$prefix . esc_attr_x( 'Search &hellip;', 'placeholder', 'wp-surf-theme' ) . '"',
			$prefix . esc_attr_x( 'Search', 'placeholder', 'wp-surf-theme' ) . '"',
		];

		$form = get_search_form( $args );

		return str_replace( $search, $replace, $form );
	}

}
