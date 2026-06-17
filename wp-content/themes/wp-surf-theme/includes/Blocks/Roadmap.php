<?php

namespace SURF\Blocks;

use SURF\Core\Blocks\Block;
use WP_Block;

/**
 * Class Roadmap
 * @package SURF\Blocks
 */
class Roadmap extends Block
{

	protected ?array $attributes = [
		'title'           => [
			'type' => 'string',
		],
		'subtitle'        => [
			'type' => 'string',
		],
		'icons'           => [
			'type' => 'boolean',
		],
		'display'         => [
			'type'    => 'string',
			'default' => 'flow',
		],
		'backgroundColor' => [
			'type' => 'string',
		],
		'textColor'       => [
			'type' => 'integer',
		],
	];

	/**
	 * @return array[]
	 */
	public function getBlockTypeArgs(): array
	{
		return [
			'provides_context' => [
				'surf/roadmap/display' => 'display',
				'surf/roadmap/icons'   => 'icons',
			],
		];
	}

	/**
	 * @param array $attributes
	 * @param string $content
	 * @param WP_Block $wpBlock
	 * @return string
	 */
	public function render( array $attributes, string $content, WP_Block $wpBlock ): string
	{
		return (string) surfView( $this->getView(), [
			'blockAttributes' => $attributes,
			'blockName'       => $this->getName(),
			'content'         => $content,
			'block'           => $this,
		] );
	}

}
