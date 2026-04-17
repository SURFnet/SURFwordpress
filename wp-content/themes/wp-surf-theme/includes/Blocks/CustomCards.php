<?php

namespace SURF\Blocks;

use SURF\Core\Blocks\Block;
use WP_Block;

/**
 * Class CustomCards
 * @package SURF\Blocks
 */
class CustomCards extends Block
{

	protected ?array $attributes = [
		'title'           => [
			'type' => 'string',
		],
		'subtitle'        => [
			'type' => 'string',
		],
		'display'         => [
			'type'    => 'string',
			'default' => 'grid',
		],
		'backgroundColor' => [
			'type' => 'string',
		],
		'textColor'       => [
			'type' => 'string',
		],
		'hideOnMobile'    => [
			'type'    => 'boolean',
			'default' => false,
		],
	];

	/**
	 * @return array[]
	 */
	public function getBlockTypeArgs(): array
	{
		return [
			'provides_context' => [
				'surf/custom-cards/display' => 'display',
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
