<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the web site, you can copy this file to "wp-config.php"
 * and fill in the values.
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
define( 'DB_NAME', 'wp-flb' );

/** MySQL database username */
define( 'DB_USER', 'root' );

/** MySQL database password */
define( 'DB_PASSWORD', '' );

/** MySQL hostname */
define( 'DB_HOST', '127.0.0.1' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication unique keys and salts.
 *
 * Change these to different unique phrases! You can generate these using
 * the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}.
 *
 * You can change these at any point in time to invalidate all existing cookies.
 * This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         ' vOIYE$b=)|I|`Hj<tx AMC=nwq4-ld!07h,.g!jf|I-vx(XsW+UOAiVzY$;!/&G' );
define( 'SECURE_AUTH_KEY',  ']WNe(!MN=taQSVLQp:?z)[N2i-DND<BQIr2F$hd}DZbgQ[mXb`1X5vY`;PlOmynz' );
define( 'LOGGED_IN_KEY',    '^rw@:gU1t~Gi]<vT@(9Us,%^2(&$>@}>c^Ebun!TKT{c),.-@Y5rJN-g[s,V.]ui' );
define( 'NONCE_KEY',        '<y+xTuiAaI58}3R&t@N/p>q$ ]:^C6%YnEbi,?A&xzf7Z!Krd|@7z^`oZ>c9TQ8&' );
define( 'AUTH_SALT',        'iY*},_hgIy>D4i*Hes Nr3b&HtByI92o+BPf mbe6cUd(Iu9~g1eAYK!b,)1{J~e' );
define( 'SECURE_AUTH_SALT', 'Hj|ylWmLow-;x:FZqYK6!yU,*LYGv]zb;T%1:fCY`v/mdE^JEt2T8bxK]5j._,f!' );
define( 'LOGGED_IN_SALT',   '|zj4*|3(;bWaK)r=rgYMmIEI-/j:<@Q*V:w AXXoSKtDuF_zz](W^SGz^U%JgSu@' );
define( 'NONCE_SALT',       'iLCD,xCs6bq}:j6.NA}>+q8ufz3O&W4pV<p~sv+|).l$V*$t%6Q$GdXaEHQd@PoS' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';

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
define( 'WP_DEBUG', false );

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
