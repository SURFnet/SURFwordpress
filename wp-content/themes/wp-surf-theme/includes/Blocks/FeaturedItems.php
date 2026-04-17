<?php

namespace SURF\Blocks;

use SURF\Core\Blocks\Block;
use SURF\Core\PostTypes\BasePost;
use SURF\PostTypes\Agenda;
use SURF\PostTypes\Asset;
use SURF\PostTypes\Download;
use SURF\PostTypes\Page;
use SURF\PostTypes\Post;
use SURF\PostTypes\Vacancy;
use WP_Block;

/**
 * Class FeaturedItems
 * @package SURF\Blocks
 */
class FeaturedItems extends Block
{

	protected ?array $attributes = [
		'title'              => [
			'type' => 'string',
		],
		'intro'              => [
			'type' => 'string',
		],
		'layout'             => [
			'type' => 'string',
			'enum' => [ 'simple', 'auto' ],
		],
		'hideCategories'     => [
			'type'    => 'boolean',
			'default' => false,
		],
		'hideDates'          => [
			'type'    => 'boolean',
			'default' => false,
		],
		'posts'              => [
			'type'    => 'array',
			'default' => [],
		],
		'hideImagesOnMobile' => [
			'type'    => 'boolean',
			'default' => false,
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
		$args = [
			'post_type' => [
				Post::getName(),
				Page::getName(),
				Agenda::getName(),
				Download::getName(),
				Vacancy::getName(),
				Asset::getName(),
			],
			'post__in'  => count( $attributes['posts'] ?? [] ) > 0 ? $attributes['posts'] : [ 0 ],
			'orderby'   => 'post__in',
		];

		return (string) surfView( $this->getView(), [
			'blockAttributes'    => $attributes,
			'blockName'          => $this->getName(),
			'content'            => $content,
			'posts'              => BasePost::query( $args ),
			'block'              => $this,
			'hideImagesOnMobile' => $attributes['hideImagesOnMobile'],
		] );
	}

}
