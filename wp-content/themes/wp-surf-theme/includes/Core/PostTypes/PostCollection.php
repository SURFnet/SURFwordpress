<?php

namespace SURF\Core\PostTypes;

use Illuminate\Support\Collection;
use WP_Post;
use WP_Query;

/**
 * A collection of AbstractPostType items
 * Class PostCollection
 * @package SURF\Core\PostTypes
 */
class PostCollection extends Collection
{

	/**
	 * @param WP_Query|array $query
	 * @return PostCollection
	 */
	public static function fromQuery( WP_Query|array $query ): static
	{
		$query   = is_array( $query ) ? new WP_Query( $query ) : $query;
		$matcher = new PostMatcher();

		return static::make( $query->posts )->map( function ( WP_Post $post ) use ( $matcher )
		{
			$class = $matcher->getClass( $post->post_type );

			if ( !is_a( $class, BasePost::class, true ) ) {
				return BasePost::fromPost( $post );
			}

			return $class::fromPost( $post );
		} );
	}

}
