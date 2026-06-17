<?php

namespace SURF\Core\Taxonomies;

use Illuminate\Contracts\Container\BindingResolutionException;
use SURF\Core\PostTypes\BasePost;

/**
 * Class TermMatcher
 * @package SURF\Core\Taxonomies
 */
class TermMatcher
{

	protected static array $map;

	/**
	 * @param string $taxonomy
	 * @return string
	 * @throws BindingResolutionException
	 */
	public function getClass( string $taxonomy ): string
	{
		$taxonomies = surfApp( TaxonomyRepository::class )->all();

		if ( !isset( $taxonomies[ $taxonomy ] ) ) {
			return Taxonomy::class;
		}

		return $taxonomies[ $taxonomy ];
	}

}
