<?php

namespace SURF\Admin\Widgets;

use SURF\Helpers\SocialHelper;
use WP_Widget;

/**
 * Class SocialMenuWidget
 * @package SURF\Admin\Widgets
 */
class SocialMenuWidget extends WP_Widget
{

	/**
	 * Class constructor
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct(
			'surf_social_menu',
			_x( 'Social Menu', 'admin', 'wp-surf-theme' ),
			[
				'description' => _x( 'Show the social links.', 'admin', 'wp-surf-theme' ),
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
		$widget  = $this;
		$socials = $this->getSocials();

		echo surfView(
			'admin.widgets.social-menu',
			compact( 'widget', 'instance', 'socials' )
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
		$title   = apply_filters( 'widget_title', $instance['title'] );
		$socials = [];
		$links   = SocialHelper::getFollowList();
		foreach ( $this->getSocials() as $key => $social ) {
			if ( empty( $links[ $key ] ) ) {
				continue;
			}
			if ( ( $instance[ $key . '_show' ] ?? '' ) !== 'on' ) {
				continue;
			}
			$socials[ $key ] = $links[ $key ];
		}

		echo surfView(
			'widgets.social-menu',
			compact( 'args', 'title', 'socials' )
		);
	}

	/**
	 * Returns the available socials
	 * @return array
	 */
	protected function getSocials(): array
	{
		return array_map( function ( $label )
		{
			return [ 'label' => $label ];
		}, SocialHelper::allFollowOptions() );
	}

}
