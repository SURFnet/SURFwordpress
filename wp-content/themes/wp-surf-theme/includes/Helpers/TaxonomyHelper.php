<?php

namespace SURF\Helpers;

use SURF\Core\Taxonomies\Taxonomy;
use SURF\Core\Taxonomies\TaxonomyRepository;
use SURF\PostTypes\Page;
use SURF\PostTypes\Post;
use SURF\Taxonomies\Category;

/**
 * Post helper methods
 * Class TaxonomyHelper
 * @package SURF\Helpers
 */
class TaxonomyHelper
{

	/**
	 * @param string $taxonomy
	 * @return string|Taxonomy|null
	 */
	public static function getTaxonomyFromName( string $taxonomy ): null|string|Taxonomy
	{
		/** @var TaxonomyRepository $repo */
		$repo = surfApp()[ TaxonomyRepository::class ];

		return $repo->all()[ $taxonomy ] ?? null;
	}

}
