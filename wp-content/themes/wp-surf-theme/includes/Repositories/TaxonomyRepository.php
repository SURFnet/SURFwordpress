<?php

namespace SURF\Repositories;

use WP_Term;

/**
 * Class TaxonomyRepository
 * @package SURF\Repositories
 */
class TaxonomyRepository
{

	/**
	 * @param string $taxonomy
	 * @param int|null $parent
	 * @return WP_Term[]
	 */
	public static function orderedByPriority( string $taxonomy, ?int $parent = null ): array
	{
		$withPriority = get_terms( [
			'taxonomy'   => $taxonomy,
			'hide_empty' => true,
			'parent'     => $parent,
			'fields'     => 'ids',
			'meta_key'   => 'priority',
			'orderby'    => 'meta_value_num',
			'meta_query' => [
				[
					[
						'key'     => 'priority',
						'compare' => 'EXISTS',
					],
					[
						'key'     => 'priority',
						'value'   => '',
						'compare' => '!=',
					],
				],
			],
		] );

		$withoutPriority = get_terms( [
			'taxonomy'   => $taxonomy,
			'hide_empty' => true,
			'parent'     => $parent,
			'fields'     => 'ids',
			'orderby'    => 'title',
			'meta_query' => [
				[
					'relation' => 'OR',
					[
						'key'     => 'priority',
						'compare' => 'NOT EXISTS',
					],
					[
						'key'     => 'priority',
						'value'   => '',
						'compare' => '==',
					],
				],
			],
		] );

		return get_terms( [
			'taxonomy'   => $taxonomy,
			'hide_empty' => true,
			'parent'     => $parent,
			'include'    => array_merge( $withPriority, $withoutPriority ),
			'orderby'    => 'include',
		] );
	}

}
