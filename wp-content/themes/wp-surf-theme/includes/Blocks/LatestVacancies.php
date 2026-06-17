<?php

namespace SURF\Blocks;

use SURF\Core\Blocks\Block;
use SURF\PostTypes\Vacancy;
use WP_Block;

/**
 * Class LatestVacancies
 * @package SURF\Blocks
 */
class LatestVacancies extends Block
{

	protected ?array $attributes = [
		'title'              => [
			'type' => 'string',
		],
		'intro'              => [
			'type' => 'string',
		],
		'count'              => [
			'type'    => 'number',
			'default' => 3,
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
		return (string) surfView( $this->getView(), [
			'blockAttributes'    => $attributes,
			'blockName'          => $this->getName(),
			'content'            => $content,
			'hideImagesOnMobile' => $attributes['hideImagesOnMobile'],
			'vacancies'          => Vacancy::query( [
				'posts_per_page' => $attributes['count'],
				'status'         => 'publish',
				'lang'           => function_exists( 'pll_current_language' ) ? pll_current_language() : null,
			], true ),
			'block'              => $this,
		] );
	}

}
