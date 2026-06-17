<?php

namespace SURF\Shortcodes;

use SURF\Core\Shortcodes\Shortcode;
use SURF\Enums\Theme;
use SURF\PostTypes\Vacancy;

/**
 * Class Roadmap
 * @package SURF\Shortcodes
 */
class Roadmap extends Shortcode
{

	/**
	 * @param $attributes
	 * @param string $content
	 * @param string $tag
	 * @return string
	 */
	public function render( $attributes, string $content, string $tag ): string
	{
		$option = 'roadmap_default_steps';
		$fields = [
			'icon'     => '',
			'title'    => '',
			'subtitle' => '',
		];
		$steps  = Theme::getRepeaterOption( $option, $fields );
		if ( empty( $steps ) ) {
			return '';
		}

		array_walk( $steps, function ( &$step, $key )
		{
			$step = $this->getAsBlock(
				'surf/step',
				// merge the step args with the order attribute. index + 1
				array_merge( $step, [ 'order' => $key + 1 ] )
			);
		} );

		$parsedBlock = $this->getAsBlock(
			'surf/roadmap',
			[
				'title'    => Theme::getOption( 'roadmap_default_title' ),
				'subtitle' => Theme::getOption( 'roadmap_default_subtitle' ),
				'icons'    => !empty( Theme::getOption( 'roadmap_default_icons' ) ),
				'display'  => Theme::getOption( 'roadmap_default_display' ),
			],
			$steps
		);

		return render_block( $parsedBlock );
	}

	/**
	 * @param string $name
	 * @param array $attrs
	 * @param array $innerBlocks
	 * @param string $innerHTML
	 * @return array
	 */
	protected function getAsBlock( string $name, array $attrs = [], array $innerBlocks = [], string $innerHTML = '' ): array
	{
		return [
			'blockName'    => $name,
			'attrs'        => $attrs,
			'innerBlocks'  => $innerBlocks,
			'innerHTML'    => $innerHTML,
			'innerContent' => array_fill( 0, count( $innerBlocks ), null ),
		];
	}

}
