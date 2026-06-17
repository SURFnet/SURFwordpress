<?php

namespace SURF\Traits;

use SURF\Enums\Theme;

/**
 * Trait HasArchiveWidgetAreaFilters
 * @package SURF\Traits
 */
trait HasArchiveWidgetAreaFilters
{

	public const PREFIX_WIDGET_POSITION   = 'widget_position_';
	public const PREFIX_COLUMN_COUNT_EXCL = 'column_count_';
	public const PREFIX_COLUMN_COUNT_INCL = 'column_count_with_widget_area_';

	/**
	 * @return string
	 */
	abstract public static function getName(): string;

	/**
	 * @return string
	 */
	abstract public static function getSingularLabel(): string;

	/**
	 * @return void
	 */
	public static function registerWidgetAreas(): void
	{
		add_action( 'widgets_init', [ static::class, 'registerWidgetArea' ] );
		add_action( 'acf/init', [ static::class, 'registerArchiveSettingsPage' ], 11 );
		add_action( 'acf/init', [ static::class, 'registerArchiveSettingsFields' ], 12 );
	}

	/**
	 * @return void
	 */
	public static function registerWidgetArea(): void
	{
		register_sidebar( [
			'name'        => static::getWidgetAreaName(),
			'id'          => static::getWidgetAreaId(),
			'description' => static::getWidgetAreaDescription(),
		] );
	}

	/**
	 * @return string
	 */
	public static function getWidgetAreaName(): string
	{
		return sprintf( _x( '%s Widget Area', 'admin', 'wp-surf-theme' ), static::getSingularLabel() );
	}

	/**
	 * @return string
	 */
	public static function getWidgetAreaId(): string
	{
		return sprintf( '%s-widget-area', static::getName() );
	}

	/**
	 * @return string
	 */
	public static function getWidgetAreaDescription(): string
	{
		return sprintf( _x( 'Widget area for %s archive', 'admin', 'wp-surf-theme' ), static::getSingularLabel() );
	}

	/**
	 * @return string
	 */
	public static function getArchiveSettingsName(): string
	{
		return static::getName() . '_archive_settings';
	}

	/**
	 * @return string
	 */
	public static function getPageTitle(): string
	{
		return sprintf( _x( '%s Archive Settings', 'admin', 'wp-surf-theme' ), static::getSingularLabel() );
	}

	/**
	 * @return void
	 */
	public static function registerArchiveSettingsPage(): void
	{
		if ( !function_exists( 'acf_add_options_page' ) ) {
			return;
		}

		acf_add_options_page( [
			'page_title'  => static::getPageTitle(),
			'menu_title'  => static::getPageTitle(),
			'menu_slug'   => static::getArchiveSettingsName(),
			'capability'  => 'manage_options',
			'redirect'    => false,
			'parent_slug' => static::getParent(),
		] );
	}

	/**
	 * @return string
	 */
	public static function getParent(): string
	{
		return static::getName() === 'post' ? 'edit.php' : 'edit.php?post_type=' . static::getName();
	}

	/**
	 * @return void
	 */
	public static function registerArchiveSettingsFields(): void
	{
		$groups = static::getArchiveSettingsFields();
		$groups = count( array_filter( array_keys( $groups ), 'is_string' ) ) > 0
			? [ $groups ]
			: $groups;

		foreach ( $groups as $fields ) {
			if ( empty( $fields ) ) {
				return;
			}

			if ( !isset( $fields['location'] ) ) {
				$fields['location'] = [
					[
						[
							'param'    => 'options_page',
							'operator' => '==',
							'value'    => static::getArchiveSettingsName(),
						],
					],
				];
			}

			acf_add_local_field_group( $fields );
		}
	}

	/**
	 * @return array
	 */
	public static function getArchiveSettingsFields(): array
	{
		return [
			'key'    => static::getName() . '_group_archive_settings',
			'title'  => _x( 'Archive settings', 'admin', 'wp-surf-theme' ),
			'fields' => [
				[
					'key'     => 'field_' . static::PREFIX_WIDGET_POSITION . static::getName(),
					'name'    => static::PREFIX_WIDGET_POSITION . static::getName(),
					'label'   => _x( 'Widget area position', 'admin', 'wp-surf-theme' ),
					'type'    => 'select',
					'choices' => [
						'hidden' => _x( 'Hidden', 'admin', 'wp-surf-theme' ),
						'top'    => _x( 'Top', 'admin', 'wp-surf-theme' ),
						'left'   => _x( 'Left', 'admin', 'wp-surf-theme' ),
						'right'  => _x( 'Right', 'admin', 'wp-surf-theme' ),
					],
				],
				[
					'key'           => 'field_' . static::PREFIX_COLUMN_COUNT_EXCL . static::getName(),
					'name'          => static::PREFIX_COLUMN_COUNT_EXCL . static::getName(),
					'label'         => _x( 'Column count without widget area', 'admin', 'wp-surf-theme' ),
					'type'          => 'select',
					'choices'       => [
						'1' => _x( '1 Column', 'admin', 'wp-surf-theme' ),
						'2' => _x( '2 Columns', 'admin', 'wp-surf-theme' ),
						'3' => _x( '3 Columns', 'admin', 'wp-surf-theme' ),
					],
					'default_value' => '3',
					'wrapper'       => [ 'width' => 50 ],
				],
				[
					'key'           => 'field_' . static::PREFIX_COLUMN_COUNT_INCL . static::getName(),
					'name'          => static::PREFIX_COLUMN_COUNT_INCL . static::getName(),
					'label'         => _x( 'Column count with widget area', 'admin', 'wp-surf-theme' ),
					'type'          => 'select',
					'choices'       => [
						'1' => _x( '1 Column', 'admin', 'wp-surf-theme' ),
						'2' => _x( '2 Columns', 'admin', 'wp-surf-theme' ),
					],
					'default_value' => '1',
					'wrapper'       => [ 'width' => 50 ],
				],
			],
		];
	}

	/**
	 * @return string
	 */
	public static function getArchiveWidgetAreaPosition(): string
	{
		$key   = static::PREFIX_WIDGET_POSITION . static::getName();
		$value = Theme::getGlobalOption( $key );

		return !empty( $value ) ? $value : 'hidden';
	}

	/**
	 * @return int
	 */
	public static function getColumnCount(): int
	{
		$key   = static::PREFIX_COLUMN_COUNT_EXCL . static::getName();
		$value = (int) Theme::getGlobalOption( $key );

		return !empty( $value ) ? $value : 3;
	}

	/**
	 * @return int
	 */
	public static function getColumnCountWithWidgetArea(): int
	{
		$key   = static::PREFIX_COLUMN_COUNT_INCL . static::getName();
		$value = (int) Theme::getGlobalOption( $key );

		return !empty( $value ) ? $value : 1;
	}

}
