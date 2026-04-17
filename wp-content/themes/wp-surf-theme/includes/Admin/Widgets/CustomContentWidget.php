<?php

namespace SURF\Admin\Widgets;

use WP_Widget;
use WP_Widget_Text;

/**
 * Class CustomContentWidget
 * @package SURF\Admin\Widgets
 */
class CustomContentWidget extends WP_Widget_Text
{

	/**
	 * Class constructor
	 * @return void
	 */
	public function __construct()
	{
		$widgetOps  = [
			'description'                 => _x( 'Show custom content.', 'admin', 'wp-surf-theme' ),
			'customize_selective_refresh' => true,
			'show_instance_in_rest'       => true,
		];
		$controlOps = [
			'width'  => 400,
			'height' => 350,
		];

		WP_Widget::__construct(
			'surf_custom_content',
			_x( 'Custom Content', 'admin', 'wp-surf-theme' ),
			$widgetOps,
			$controlOps
		);
	}

	/**
	 * Renders the widget
	 * @param array $args
	 * @param array $instance
	 */
	public function widget( $args, $instance ): void
	{
		$args['before_widget'] = '';
		$args['after_widget']  = '';
		$args['before_title']  = '';
		$args['after_title']   = '';

		ob_start();
		parent::widget( $args, $instance );
		$text  = ob_get_clean();
		$title = $instance['title'] ?? '';

		echo surfView(
			'widgets.custom-content',
			compact( 'args', 'title', 'text' )
		);
	}

}
