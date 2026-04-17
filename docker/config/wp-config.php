<?php
/**
 * The base configuration for WordPress.
 *
 * The wp-config.php creation script uses this file during the
 * installation. You don't have to use the website, you can
 * copy this file to "wp-config.php" and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 */

// ** MySQL settings - You can get this info from your web host ** //
/* The name of the database for WordPress */
define('DB_NAME', 'wp-surf-theme');

/* MySQL database username */
define('DB_USER', 'surf');

/* MySQL database password */
define('DB_PASSWORD', 'x37YPHCq54Yh4E');

/* MySQL hostname */
define('DB_HOST', 'mysql');

/* Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');

/* The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY', '[|s|fk2^5%TJp^zv(5){yu6CU/+JGIvn$DC|V)g_] |Q+m%;Q?V--MM=[zf-=m`i');
define('SECURE_AUTH_KEY', '<?NINJQ8496&hkUUCY+K<c9@9]}lYJ:Z$o>B8v]es2*{bHPw~@5X*2.,b|9+.%36');
define('LOGGED_IN_KEY', '/d+9d3YroluouA0{+QJc>u;bmD_Yc9b34&&U!LCvFWP(h4_Y*7S }?tpR9CZ4Qy/');
define('NONCE_KEY', '-gP-O>*w@1Up.R*f;-sm*:ZX*wEE+rlro|+`rZ|TlKq6d0tE`GU/0Qyd~Y|c@4uB');
define('AUTH_SALT', '~$1?gizEcEXP,5Vg9@%YZ8}3-gSG]/(3j&wZ4;(k02;u*jwR=mf-*=Wie1Y4JIEI');
define('SECURE_AUTH_SALT', 'D9B|hbsh(~VMqhW qq4<ocIRd@ylJwYl6.+>YCix|lzi+7DI9|~MK}W7-B%{Q=A&');
define('LOGGED_IN_SALT', '^x < mI*YvC;pXhA2c8/fy.CP&2-l2F*(T~qxGpKB9g[3kr~+KiFjb>Mw >#<W]7');
define('NONCE_SALT', 'b%i`nI-TH{#wyR+vILR*D%h~FZ-;Af[aR Y11/%;<! bGwkp 2t[lZkxR`P/)nm_');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'surf_';

/*
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the documentation.
 *
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
*/
define('WP_DEBUG', true);
define('WP_DEBUG_DISPLAY', true);
define('WP_DEBUG_LOG', false);

define('WP_ENVIRONMENT_TYPE', 'development');

/* Make site_url and home dynamic for docker */
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https" : "http";
define('WP_SITEURL', $protocol . '://' . $_SERVER['HTTP_HOST']);
define('WP_HOME', $protocol . '://' . $_SERVER['HTTP_HOST']);

/* That's all, stop editing! Happy publishing. */

/* Absolute path to the WordPress directory. */
if (! defined('ABSPATH')) {
    define('ABSPATH', __DIR__ . '/');
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
