<?php

namespace SURF\Core\PostTypes;

use Illuminate\Contracts\Container\BindingResolutionException;

/**
 * Class PostMatcher
 * Matches post types to their corresponding classes
 * @package SURF\Core\PostTypes
 */
class PostMatcher
{

	protected static array $map;

	/**
	 * @param string $postType
	 * @return mixed|string
	 * @throws BindingResolutionException
	 */
	public function getClass( string $postType )
	{
		$postTypes = surfApp( PostTypeRepository::class )->all();

		if ( !isset( $postTypes[ $postType ] ) ) {
			return BasePost::class;
		}

		return $postTypes[ $postType ];
	}

}
