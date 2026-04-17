<?php

namespace SURF\Blocks;

use Illuminate\Http\Request;
use SURF\Core\Blocks\Block;
use SURF\PostTypes\Post;
use SURF\Taxonomies\Category;
use WP_Block;

/**
 * Class Articles
 * @package SURF\Blocks
 */
class Articles extends Block
{

	protected ?array $attributes = [
		'title'              => [
			'type' => 'string',
		],
		'intro'              => [
			'type' => 'string',
		],
		'category'           => [
			'type' => 'integer',
		],
		'buttonText'         => [
			'type' => 'string',
		],
		'count'              => [
			'type' => 'integer',
		],
		'layout'             => [
			'type' => 'string',
			'enum' => [ 'simple', 'auto' ],
		],
		'hideImagesOnMobile' => [
			'type' => 'boolean',
		],
		'dateDisplay'        => [
			'type' => 'string',
			'enum' => [ 'default', 'hidden', 'published', 'modified', 'both' ],
		],
	];

	/**
	 * @param array $attributes
	 * @param string $content
	 * @param WP_Block $wpBlock
	 * @return string
	 */
	public function render( array $attributes, string $content, WP_Block $wpBlock ): string
	{
		$attributes = array_merge(
			$attributes,
			[
				'count' => $attributes['count'] ?? 3,
			],
		);

		$args = [ 'posts_per_page' => $attributes['count'] ];

		if ( isset( $attributes['category'] ) && $attributes['category'] ) {
			$args['tax_query'] = [
				[
					'taxonomy' => Category::getName(),
					'terms'    => [ $attributes['category'] ],
				],
			];
		}

		$archiveLink = isset( $attributes['category'] )
			? get_term_link( $attributes['category'], Category::getName() )
			: Post::getArchiveLink();

		$postQuery    = Post::rawQuery( $args );
		$posts        = Post::fromQuery( $postQuery );
		$hasMorePosts = $postQuery->found_posts > count( $posts );

		return (string) surfView( $this->getView(), [
			'blockAttributes'    => $attributes,
			'blockName'          => $this->getName(),
			'content'            => $content,
			'hideImagesOnMobile' => $attributes['hideImagesOnMobile'] ?? false,
			'posts'              => $posts,
			'hasMorePosts'       => $hasMorePosts,
			'block'              => $this,
			'archiveLink'        => $archiveLink,
			'layout'             => $attributes['layout'] ?? 'simple',
			'dateDisplay'        => $attributes['dateDisplay'] ?? 'default',
		] );
	}

}
