<?php

namespace SURF\Blocks;

use SURF\Core\Blocks\AcfBlock;

/**
 * Class ExampleAcfBlock
 * @package SURF\Blocks
 */
class ExampleAcfBlock extends AcfBlock
{

	/**
	 * Register the fields for the block.
	 * This is the 'fields' part of the ACF array.
	 * @link https://www.advancedcustomfields.com/resources/register-fields-via-php
	 * @return array[]
	 */
	public function getFields(): array
	{
		return [
			[
				'key'   => $this->getFieldKey( 'title' ),
				'label' => 'Title',
				'name'  => 'title',
				'type'  => 'text',
			],
			[
				'key'   => $this->getFieldKey( 'image' ),
				'label' => 'Image',
				'name'  => 'image',
				'type'  => 'image',
			],
		];
	}

}
