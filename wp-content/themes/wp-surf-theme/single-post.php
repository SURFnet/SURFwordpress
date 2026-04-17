<?php

namespace SURF;

use Illuminate\Contracts\View\View;
use SURF\Core\Controllers\TemplateController;
use SURF\Core\PostTypes\BasePost;
use SURF\Core\PostTypes\PostCollection;
use SURF\PostTypes\Post;

/**
 * Class SinglePostController
 * @package SURF
 */
class SinglePostController extends TemplateController
{

	/**
	 * @param Post $post
	 * @return View
	 */
	public function handle( Post $post ): View
	{
		$relatedPosts = $this->getRelatedPosts( $post );

		return $this->view( 'post.single', compact( 'post', 'relatedPosts' ) );
	}

	/**
	 * @param BasePost $post
	 * @return PostCollection
	 */
	public function getRelatedPosts( BasePost $post ): PostCollection
	{
		$args = [
			'post_status'    => 'publish',
			'posts_per_page' => 3,
			'no_found_rows'  => true,
			'post__not_in'   => [ $post->ID() ],
		];

		$categories = wp_get_post_categories( $post->ID() );
		if ( is_wp_error( $categories ) || empty( $categories ) ) {
			return Post::query( $args );
		}

		$currentCategory = $categories[0] ?? 0;
		if ( !empty( $currentCategory ) ) {
			$args['cat'] = $currentCategory;
		}

		return Post::query( $args );
	}

}
