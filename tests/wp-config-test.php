<?php

/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the
 * installation. You don't have to use the web site, you can
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
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', getenv('TEST_SITE_DB_NAME'));

/** MySQL database username */
define('DB_USER', getenv('TEST_SITE_DB_USER'));

/** MySQL database password */
define('DB_PASSWORD', getenv('TEST_SITE_DB_PASSWORD'));

/** MySQL hostname */
define('DB_HOST', getenv('TEST_SITE_DB_HOST'));

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');

/** The Database Collate type. Don't change this if in doubt . *  /
define('DB_COLLATE', '') {
}

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY', 'd830184037a5c43ee98507ef5cdccc22203ecf8c');
define('SECURE_AUTH_KEY', '9464e683d050caccc7d642bdb625ac72f5873f40');
define('LOGGED_IN_KEY', '60a95488229c037ff0136ede12830e58612486e5');
define('NONCE_KEY', 'b1c7eea265557cecc6fb369e9cc86119bc05e8bf');
define('AUTH_SALT', 'b85336e1878d60abf30e33b03fbec071487819b5');
define('SECURE_AUTH_SALT', '13ccb5d352744fed9fac0641e40bc072fdfc43d2');
define('LOGGED_IN_SALT', '7dc590cc681f2aca995ad35e6028d1fa8b20c091');
define('NONCE_SALT', '0deee11bd5d0b5a9d82adf4ca63855569fc7c25d');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_51_wc_41_';

/**
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

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if (! defined('ABSPATH')) {
    define('ABSPATH', __DIR__ . '/');
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';

/** Additional options */

define('WP_SITEURL', getenv('TEST_SITE_WP_URL'));
define('WP_HOME', getenv('TEST_SITE_WP_URL'));
define('WP_ADMIN_DIR', 'wp-admin');
