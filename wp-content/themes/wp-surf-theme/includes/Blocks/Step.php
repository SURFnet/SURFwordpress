<?php

namespace SURF\Blocks;

use SURF\Core\Blocks\Block;
use WP_Block;

/**
 * Class Step
 * @package SURF\Blocks
 */
class Step extends Block
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
		'order'    => [
			'type'    => 'integer',
			'default' => 1,
		],
	];

	/**
	 * @return array[]
	 */
	public function getBlockTypeArgs(): array
	{
		return [
			'uses_context' => [
				'surf/roadmap/display',
				'surf/roadmap/icons',
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
		$display = $wpBlock->context['surf/roadmap/display'] ?? 'flow';
		$icons   = $wpBlock->context['surf/roadmap/icons'] ?? false;

		return (string) surfView( $this->getView(), [
			'blockAttributes' => $attributes,
			'blockName'       => $this->getName(),
			'content'         => $content,
			'block'           => $this,
			'context'         => [
				'display' => $display,
				'icons'   => $icons,
			],
		] );
	}

}
