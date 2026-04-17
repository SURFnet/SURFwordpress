<?php

namespace SURF\Core\Traits;

use SURF\Helpers\PolylangHelper;
use SURF\Plugins\AcfPro\AcfPro;

/**
 * Adds a settings page for each language in polylang kinda like Theme settings
 */
trait HasLocalizedSettingsPage
{

	/**
	 * @return string
	 */
	abstract public static function getName(): string;

	/**
	 * @return array
	 */
	abstract public static function getLocalizedSettingsFields(): array;

	/**
	 * @param string $lang
	 * @return string
	 */
	public static function getLocalizedSettingsName( string $lang ): string
	{
		return static::getName() . '_settings_' . $lang;
	}

	/**
	 * @return string
	 */
	public static function getLocalizedSettingsPrefix(): string
	{
		$location = 'options';

		if ( function_exists( 'pll_current_language' ) ) {
			$location = PolylangHelper::getCurrentLanguageSlug();
		}

		return $location;
	}

	/**
	 * @return void
	 */
	public static function registerLocalizedSettings(): void
	{
		if ( !function_exists( 'acf_add_options_page' ) ) {
			return;
		}

		add_action( 'acf/init', [ static::class, 'registerLocalizedSettingsPage' ], 11 );
		add_action( 'acf/init', [ static::class, 'registerLocalizedSettingsFields' ], 12 );
	}

	/**
	 * @param string $lang
	 * @return string
	 */
	public static function getLocalizedSettingsPageTitle( string $lang = '' ): string
	{
		if ( $lang ) {
			return sprintf(
				_x( 'Settings (%s)', 'admin', 'wp-surf-theme' ),
				$lang
			);
		}

		return _x( 'Settings', 'admin', 'wp-surf-theme' );
	}

	/**
	 * @param string $lang
	 * @return string
	 */
	public static function getLocalizedSettingsMenuTitle( string $lang = '' ): string
	{
		if ( $lang ) {
			return sprintf(
				_x( 'Settings (%s)', 'admin', 'wp-surf-theme' ),
				$lang
			);
		}

		return _x( 'Settings', 'admin', 'wp-surf-theme' );
	}

	/**
	 * @return void
	 */
	public static function registerLocalizedSettingsPage()
	{
		if ( !function_exists( 'acf_add_options_page' ) ) {
			return;
		}

		$languages = PolylangHelper::getLanguages();
		if ( !$languages ) {
			$languages = [ 'default' ];
		}

		foreach ( $languages as $key => $language ) {
			$title = strtoupper( $language );

			if ( $language === 'default' ) {
				$title = __( 'Default', 'wp-surf-theme' );
			}

			acf_add_options_page( [
				'page_title'  => static::getLocalizedSettingsPageTitle( $title ),
				'menu_title'  => static::getLocalizedSettingsMenuTitle( $title ),
				'menu_slug'   => static::getLocalizedSettingsName( $language ),
				'capability'  => 'manage_options',
				'post_id'     => $language === 'default' ? 'options' : $language,
				'redirect'    => false,
				'parent_slug' => 'edit.php?post_type=' . static::getName(),
			] );
		}
	}

	/**
	 * Get Locations to register Language Options.
	 * @return array
	 */
	public static function getPolylangLocations(): array
	{
		$languages = PolylangHelper::getLanguages();
		$locations = [];

		if ( !$languages ) {
			// default page for when there are no languages
			$languages = [ 'default' ];
		}

		foreach ( $languages as $key => $language ) {
			$locations[] = [
				[
					'param'    => 'options_page',
					'operator' => '==',
					'value'    => static::getLocalizedSettingsName( $language ),
				],
			];
		}

		return $locations;
	}

	/**
	 * @return void
	 */
	public static function registerLocalizedSettingsFields(): void
	{
		$groups = static::getLocalizedSettingsFields();
		$groups = count( array_filter( array_keys( $groups ), 'is_string' ) ) > 0
			? [ $groups ]
			: $groups;

		foreach ( $groups as $fields ) {
			if ( empty( $fields ) ) {
				return;
			}

			if ( !isset( $fields['location'] ) ) {
				$fields['location'] = static::getPolylangLocations();
			}

			acf_add_local_field_group( $fields );
		}
	}

}
