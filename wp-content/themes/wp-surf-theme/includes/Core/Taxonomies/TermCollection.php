<?php

namespace SURF\Core\Taxonomies;

use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Collection;
use SURF\Core\Exceptions\MismatchingTaxonomyException;
use WP_Term_Query;

/**
 * Class TermCollection
 * @package SURF\Core\Taxonomies
 */
class TermCollection extends Collection
{

	/**
	 * @param WP_Term_Query|array $query
	 * @return static
	 * @throws BindingResolutionException
	 * @throws MismatchingTaxonomyException
	 */
	public static function fromQuery( WP_Term_Query|array $query ): static
	{
		$query = is_array( $query ) ? new WP_Term_Query( $query ) : $query;

		$matcher = new TermMatcher();
		$terms   = array_map( function ( $term ) use ( $matcher )
		{
			$taxonomy = $matcher->getClass( $term->taxonomy );

			return is_a( $taxonomy, Taxonomy::class, true )
				? $taxonomy::fromTerm( $term )
				: Taxonomy::fromTerm( $term );
		}, $query->get_terms() );

		return new static( $terms );
	}

}
