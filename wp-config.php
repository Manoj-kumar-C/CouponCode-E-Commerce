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
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * Localized language
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'local' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', 'root' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

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
define( 'AUTH_KEY',          'z2n*<hg4cxQDTXwk/HS3k<i*-TJ3NxAZCGL}[RhjQ4z1XERVZ:IBaQKId~)c_%0c' );
define( 'SECURE_AUTH_KEY',   'zyVan[=.zSoA*s@,!]_Q3pCBp#Ejx<Ae]zQ6)&|1|$;f)L-zP[Qno?>*q}p^ZkrN' );
define( 'LOGGED_IN_KEY',     '{:0sCPl[+@O;,0,B+<,sY@n%[ms![f?,se}Ar)3LC5lu/lB9y?rCVVQjKe)^)M[k' );
define( 'NONCE_KEY',         '~gUfm#a/vRWisyH<n78a)RST:y(iUoiTaZ4$`F|,)lG,YN@?g@Ptd/3jXy&RgUrP' );
define( 'AUTH_SALT',         'U@)++`f|%a9@=i-k662$|^U+`<}_*h7OR[v`(H*VR]|;O!BC$0Tu:d*#!AgBZqSc' );
define( 'SECURE_AUTH_SALT',  '-V<E]{(diV;xw2W>UGoe(Eo;]amlx*gz.[&jnp[JV_?h%lqIW1H0-|/A[4@7x=`&' );
define( 'LOGGED_IN_SALT',    '!<&Z1H.n#9<6(^Ls7f/ xa0h;z}!|0xkS{=+d$<x!Hzew^:Q@I<uB5G3uUs/Ycn]' );
define( 'NONCE_SALT',        'x5h|>_?O6sw gcbcd k~$0-s*E0En/a@y~#vh{mM{w?*feOJBcgQAes*dmA*15&o' );
define( 'WP_CACHE_KEY_SALT', 'D,4i(}soi^De8~|y&{A(6A?QPKqul(Mk%m~5*x/0Sy6QARShM_|av35rn2b<6uik' );


/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';


/* Add any custom values between this line and the "stop editing" line. */



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
if ( ! defined( 'WP_DEBUG' ) ) {
	define( 'WP_DEBUG', false );
}

define( 'WP_ENVIRONMENT_TYPE', 'local' );
/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
