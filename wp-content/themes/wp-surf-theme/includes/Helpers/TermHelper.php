<?php

namespace SURF\Helpers;

use JetBrains\PhpStorm\ArrayShape;
use WP_Term;

/**
 * Class TermHelper
 * @package SURF\Helpers
 */
class TermHelper
{

	/**
	 * @param string $taxonomy
	 * @param array $terms
	 * @return array
	 */
	#[ArrayShape( [ 'string' => 'array' ] )]
	public static function groupByParent( string $taxonomy, array $terms ): array
	{
		$list = [];
		foreach ( $terms as $term ) {
			$term = get_term_by( 'slug', $term, $taxonomy );
			if ( !( $term instanceof WP_Term ) || empty( $term->parent ) ) {
				continue;
			}

			$parent = get_term( $term->parent, $taxonomy );
			if ( !( $parent instanceof WP_Term ) ) {
				continue;
			}

			if ( !isset( $list[ $parent->slug ] ) ) {
				$list[ $parent->slug ] = [];
			}
			$list[ $parent->slug ][] = $term->slug;
		}

		return $list;
	}

}
