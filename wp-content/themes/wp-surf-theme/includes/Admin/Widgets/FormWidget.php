<?php

namespace SURF\Admin\Widgets;

use SURF\Helpers\GFHelper;
use WP_Widget;

/**
 * Class FormWidget
 * @package SURF\Admin\Widgets
 */
class FormWidget extends WP_Widget
{

	/**
	 * Class constructor
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct(
			'surf_form',
			_x( 'Form', 'admin', 'wp-surf-theme' ),
			[
				'description' => _x( 'Show a form.', 'admin', 'wp-surf-theme' ),
			]
		);
	}

	/**
	 * Renders the widget settings
	 * @param array $instance
	 * @return string
	 */
	public function form( $instance ): string
	{
		$widget = $this;

		$choices   = GFHelper::getChoices();
		$showTitle = (bool) ( $instance['show_title'] ?? '' );
		$formArgs  = [
			'name'    => $widget->get_field_name( 'form' ),
			'choices' => $choices,
			'value'   => $instance['form'] ?? '',
			'style'   => 'width: 100%;',
		];

		echo surfView(
			'admin.widgets.form',
			compact( 'widget', 'instance', 'formArgs', 'showTitle' )
		);

		return '';
	}

	/**
	 * Renders the widget
	 * @param array $args
	 * @param array $instance
	 */
	public function widget( $args, $instance ): void
	{
		$form      = $instance['form'] ?? '';
		$showTitle = (bool) ( $instance['show_title'] ?? '' );

		echo surfView(
			'widgets.form',
			compact( 'args', 'form', 'showTitle' )
		);
	}

}
