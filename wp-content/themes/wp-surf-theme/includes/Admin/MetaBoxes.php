<?php

namespace SURF\Admin;

use SURF\Admin\MetaBoxes\AgendaSettings;
use SURF\Admin\MetaBoxes\MenuItemSettings;
use SURF\Admin\MetaBoxes\ThemeSettings;
use SURF\Admin\MetaBoxes\UserSettings;
use SURF\Admin\MetaBoxes\ContactPersonsSettings;

/**
 * Class MetaBoxes
 * @package SURF\Admin
 */
class MetaBoxes
{

	/**
	 * Make sure MetaBoxes are registered at the right time
	 * @return void
	 */
	public static function init(): void
	{
		add_action( 'acf/init', [ static::class, 'addMetaboxes' ] );
	}

	/**
	 * Register meta boxes
	 * @return void
	 */
	public static function addMetaboxes(): void
	{
		// Settings
		ThemeSettings::init();
		AgendaSettings::init();
		MenuItemSettings::init();
		UserSettings::init();
		ContactPersonsSettings::init();
	}

	/**
	 * Registers ACF local field group
	 * @param array $args
	 * @return bool
	 */
	public static function register( array $args ): bool
	{
		if ( !function_exists( 'acf_add_local_field_group' ) ) {
			return false;
		}

		return acf_add_local_field_group( $args );
	}

}
