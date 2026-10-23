<?php

namespace SURF\Helpers;

/**
 * Class PolylangHelper
 * @package SURF\Helpers
 */
class PolylangHelper
{

	/**
	 * Polylang version in which the arguments of pll_the_languages() were renamed
	 * @see https://polylang.pro/polylang-3-9-language-switcher-pll-the-languages/
	 */
	public const PLL_LANG_ARGS_VERSION = '3.9';

	/**
	 * Whether the installed Polylang version uses the new pll_the_languages() arguments (3.9+)
	 * @return bool
	 */
	public static function useNewPllLangArgs(): bool
	{
		if ( !defined( 'POLYLANG_VERSION' ) ) {
			return false;
		}

		return version_compare( POLYLANG_VERSION, static::PLL_LANG_ARGS_VERSION, '>=' );
	}

	/**
	 * @return array
	 */
	public static function getLanguages(): array
	{
		if ( !function_exists( 'pll_languages_list' ) ) {
			return [];
		}

		return (array) pll_languages_list() ?? [];
	}

	/**
	 * @return void
	 */
	public static function theLanguages(): void
	{
		if ( !function_exists( 'pll_the_languages' ) ) {
			return;
		}

		pll_the_languages( static::getPllLangArgs() );
	}

	/**
	 * Outputs the language switcher items with the language slugs as labels
	 * @return void
	 */
	public static function theLanguagesSlugs(): void
	{
		if ( !function_exists( 'pll_the_languages' ) ) {
			return;
		}

		pll_the_languages( static::getPllLangArgs( true ) );
	}

	/**
	 * Returns pll_the_languages() arguments that output only the <li> items (the views add their own <ul>),
	 * without triggering deprecation notices on Polylang 3.9+ and still working on older versions.
	 * @param bool $show_slugs Show the language slugs (e.g. "nl") instead of the language names.
	 * @return array
	 */
	public static function getPllLangArgs( bool $show_slugs = false ): array
	{
		$args = [];
		if ( static::useNewPllLangArgs() ) {
			$args = [
				'layout'       => 'vertical',
				'show_wrapper' => false,
				'show_labels'  => 'names',
			];

			if ( $show_slugs ) {
				$args['show_labels'] = 'codes';
			}

			return $args;
		}

		if ( $show_slugs ) {
			$args['display_names_as'] = 'slug';
		}

		return $args;
	}

	/**
	 * @return string
	 */
	public static function getCurrentLanguage(): string
	{
		if ( !function_exists( 'pll_current_language' ) ) {
			return '';
		}

		return (string) pll_current_language( 'name' );
	}

	/**
	 * @return string
	 */
	public static function getCurrentLanguageSlug(): string
	{
		if ( !function_exists( 'pll_current_language' ) ) {
			return '';
		}

		return (string) pll_current_language( 'slug' );
	}

	/**
	 * Get options fields based on language
	 * @param string $key
	 * @return mixed
	 */
	public static function getThemeOption( string $key = '' ): mixed
	{
		$language = static::getCurrentLanguageSlug();
		$prefix   = $language ?: 'options';

		return get_option( $prefix . '_' . $key );
	}

	/**
	 * Get options repeater field values based on language
	 * @param string $key
	 * @param array $subFields
	 * @return mixed
	 */
	public static function getRepeaterThemeOption( string $key, array $subFields ): mixed
	{
		$list  = [];
		$count = (int) static::getThemeOption( $key );
		if ( empty( $count ) ) {
			return $list;
		}

		for ( $i = 0; $i < $count; $i++ ) {
			$values = [];
			foreach ( $subFields as $subKey => $subDefault ) {
				if ( is_int( $subKey ) ) {
					$subKey     = $subDefault;
					$subDefault = null;
				}
				$values[ $subKey ] = static::getThemeOption( $key . '_' . $i . '_' . $subKey, $subDefault );
			}
			$list[ $i ] = $values;
		}

		return $list;
	}

	/**
	 * Get default options fields
	 * @param string $optionField
	 * @return mixed
	 */
	public static function getGlobalThemeOption( string $optionField = '' ): mixed
	{
		return get_option( 'options_' . $optionField );
	}

	/**
	 * Get default options repeater field values
	 * @param string $key
	 * @param array $subFields
	 * @return mixed
	 */
	public static function getGlobalRepeaterThemeOption( string $key, array $subFields ): mixed
	{
		$list  = [];
		$count = (int) static::getGlobalThemeOption( $key );
		if ( empty( $count ) ) {
			return $list;
		}

		for ( $i = 0; $i < $count; $i++ ) {
			$values = [];
			foreach ( $subFields as $subKey => $subDefault ) {
				if ( is_int( $subKey ) ) {
					$subKey     = $subDefault;
					$subDefault = null;
				}
				$values[ $subKey ] = static::getGlobalThemeOption( $key . '_' . $i . '_' . $subKey, $subDefault );
			}
			$list[ $i ] = $values;
		}

		return $list;
	}

	/**
	 * @param string $optionField
	 * @return mixed
	 */
	public static function getThemeOptionWithGlobalFallback( string $optionField = '' ): mixed
	{
		$value = static::getThemeOption( $optionField );
		if ( !empty( $value ) ) {
			return $value;
		}

		return static::getGlobalThemeOption( $optionField );
	}

	/**
	 * @param string $key
	 * @param array $subFields
	 * @return array
	 */
	public static function getRepeaterThemeOptionWithGlobalFallback( string $key, array $subFields ): array
	{
		$value = static::getRepeaterThemeOption( $key );
		if ( !empty( $value ) ) {
			return $value;
		}

		return static::getGlobalRepeaterThemeOption( $key );
	}

	/**
	 * @param array $args
	 * @param string $postType
	 * @return array
	 */
	public static function parseQueryArgs( array $args, string $postType ): array
	{
		if (
			function_exists( 'PLL' )
			&& function_exists( 'pll_default_language' )
			&& function_exists( 'pll_is_translated_post_type' )
			&& pll_is_translated_post_type( $postType )
		) {
			$args['lang'] = Helper::getSanitizedRequest( 'lang', pll_default_language() );
			$lang         = PLL()->model->get_language( $args['lang'] );

			if ( $lang ) {
				PLL()->curlang = $lang;
				switch_to_locale( $lang->locale );
			}
		}

		return $args;
	}

}
