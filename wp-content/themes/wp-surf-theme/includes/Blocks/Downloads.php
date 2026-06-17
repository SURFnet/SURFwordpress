<?php

namespace SURF\Blocks;

use SURF\Core\Blocks\Block;
use SURF\PostTypes\Download;
use WP_Block;

/**
 * Class Downloads
 * @package SURF\Blocks
 */
class Downloads extends Block
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
		$args = [ 'posts_per_page' => 6 ];

		if ( isset( $attributes['category'] ) && $attributes['category'] ) {
			$args['tax_query'] = [
				[
					'taxonomy' => 'surf-download-category',
					'terms'    => [ $attributes['category'] ],
				],
			];
		}

		return (string) surfView( $this->getView(), [
			'blockAttributes'    => $attributes,
			'blockName'          => $this->getName(),
			'content'            => $content,
			'hideImagesOnMobile' => $attributes['hideImagesOnMobile'],
			'downloads'          => Download::query( $args ),
			'block'              => $this,
		] );
	}

}
