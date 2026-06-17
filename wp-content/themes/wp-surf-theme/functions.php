<?php
/**
 * List theme constants and boot the application
 */

use SURF\Application;

define( 'SURF_THEME_DIR', get_template_directory() );
define( 'SURF_THEME_DIR_ASSETS', SURF_THEME_DIR . '/assets' );
define( 'SURF_THEME_DIR_CSS', SURF_THEME_DIR_ASSETS . '/css' );
define( 'SURF_THEME_DIR_JS', SURF_THEME_DIR_ASSETS . '/js' );
define( 'SURF_THEME_DIR_ICONS', SURF_THEME_DIR_ASSETS . '/icons' );
define( 'SURF_THEME_DIR_COMPONENTS', SURF_THEME_DIR_ASSETS . '/components' );
define( 'SURF_THEME_DIR_IMAGES', SURF_THEME_DIR_ASSETS . '/images' );
define( 'SURF_THEME_DIR_INCLUDES', SURF_THEME_DIR . '/includes' );
define( 'SURF_THEME_DIR_VENDOR', SURF_THEME_DIR . '/vendor' );

define( 'SURF_THEME_URI', get_template_directory_uri() );
define( 'SURF_THEME_URI_ASSETS', SURF_THEME_URI . '/assets' );
define( 'SURF_THEME_URI_CSS', SURF_THEME_URI_ASSETS . '/css' );
define( 'SURF_THEME_URI_JS', SURF_THEME_URI_ASSETS . '/js' );
define( 'SURF_THEME_URI_ICONS', SURF_THEME_URI_ASSETS . '/icons' );
define( 'SURF_THEME_URI_COMPONENTS', SURF_THEME_URI_ASSETS . '/components' );
define( 'SURF_THEME_URI_IMAGES', SURF_THEME_URI_ASSETS . '/images' );
define( 'SURF_THEME_URI_INCLUDES', SURF_THEME_URI . '/includes' );
define( 'SURF_THEME_URI_VENDOR', SURF_THEME_URI . '/vendor' );

// Set CORS headers.
add_action( 'send_headers', function ()
{
	header( 'Access-Control-Allow-Origin: ' . get_site_url() );
} );

// Autoload from root.
require_once SURF_THEME_DIR . '/vendor/autoload.php';
require_once SURF_THEME_DIR . '/includes/helpers.php';

$app = Application::getInstance();
$app->boot();
