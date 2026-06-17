<?php

namespace SURF\Core\Traits;

/**
 * Trait HasSettingsPage
 * Provides functionality to register ACF settings pages and fields
 * @package SURF\Core\Traits
 */
trait HasSettingsPage
{

	/**
	 * @return string
	 */
	abstract public static function getName(): string;

	/**
	 * @return array
	 */
	abstract public static function getSettingsFields(): array;

	/**
	 * @return string
	 */
	public static function getSettingsName(): string
	{
		return static::getName() . '_settings';
	}

	/**
	 * @return void
	 */
	public static function registerSettings(): void
	{
		if ( !function_exists( 'acf_add_options_page' ) ) {
			return;
		}

		add_action( 'acf/init', [ static::class, 'registerSettingsPage' ], 11 );
		add_action( 'acf/init', [ static::class, 'registerSettingsFields' ], 12 );
	}

	/**
	 * @return string
	 */
	public static function getSettingsPageTitle(): string
	{
		return __( 'Settings', 'wp-surf-theme' );
	}

	/**
	 * @return string
	 */
	public static function getSettingsMenuTitle(): string
	{
		return __( 'Settings', 'wp-surf-theme' );
	}

	/**
	 * @return void
	 */
	public static function registerSettingsPage()
	{
		if ( !function_exists( 'acf_add_options_page' ) ) {
			return;
		}

		acf_add_options_page( [
			'page_title'  => static::getSettingsPageTitle(),
			'menu_title'  => static::getSettingsMenuTitle(),
			'menu_slug'   => static::getSettingsName(),
			'capability'  => 'manage_options',
			'redirect'    => false,
			'parent_slug' => 'edit.php?post_type=' . static::getName(),
		] );
	}

	/**
	 * @return void
	 */
	public static function registerSettingsFields(): void
	{
		$groups = static::getSettingsFields();
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
							'value'    => static::getSettingsName(),
						],
					],
				];
			}

			acf_add_local_field_group( $fields );
		}
	}

}
