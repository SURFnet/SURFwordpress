<?php

namespace SURF\Admin\Widgets;

use WP_Widget;

/**
 * Class AppStoresWidget
 * @package SURF\Admin\Widgets
 */
class AppStoresWidget extends WP_Widget
{

	/**
	 * Class constructor
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct(
			'surf_app_stores',
			_x( 'App Stores', 'admin', 'wp-surf-theme' ),
			[
				'description' => _x( 'Show the app store buttons.', 'admin', 'wp-surf-theme' ),
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
		$stores = $this->getStores();
		echo surfView(
			'admin.widgets.app-stores',
			compact( 'widget', 'instance', 'stores' )
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
		$title  = apply_filters( 'widget_title', $instance['title'] );
		$stores = collect( $this->getStores() )
			->filter( function ( array $social, string $key ) use ( $instance )
			{
				return ( $instance["{$key}_show"] ?? '' ) === 'on' && !empty( $instance["{$key}_url"] );
			} )
			->mapWithKeys( function ( $social, $key ) use ( $instance )
			{
				return [
					$key => [
						'label' => $social['label'],
						'url'   => $instance["{$key}_url"],
					],
				];
			} )
			->toArray();

		echo surfView(
			'widgets.app-stores',
			compact( 'args', 'title', 'stores' )
		);
	}

	/**
	 * @return array
	 */
	protected function getStores(): array
	{
		return [
			'android' => [
				'label' => _x( 'Android', 'admin', 'wp-surf-theme' ),
			],
			'ios'     => [
				'label' => _x( 'iOS', 'admin', 'wp-surf-theme' ),
			],
		];
	}

}
