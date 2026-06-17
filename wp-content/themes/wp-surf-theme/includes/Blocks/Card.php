<?php

namespace SURF\Blocks;

use SURF\Core\Blocks\Block;
use WP_Block;

/**
 * Class Card
 * @package SURF\Blocks
 */
class Card extends Block
{

	protected ?array $attributes = [
		'title'    => [
			'type' => 'string',
		],
		'subtitle' => [
			'type' => 'string',
		],
		'icon'     => [
			'type'    => 'string',
			'default' => 'file',
		],
		'link'     => [
			'type'    => 'string',
			'default' => '',
		],
	];

	/**
	 * @return array[]
	 */
	public function getBlockTypeArgs(): array
	{
		return [
			'uses_context' => [
				'surf/custom-cards/display',
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
		$display = $wpBlock->context['surf/custom-cards/display'] ?? 'grid';

		return (string) surfView( $this->getView(), [
			'blockAttributes' => $attributes,
			'blockName'       => $this->getName(),
			'content'         => $content,
			'block'           => $this,
			'context'         => [
				'display' => $display,
			],
		] );
	}

}
