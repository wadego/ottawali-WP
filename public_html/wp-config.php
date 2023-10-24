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
define( 'DB_NAME', 'dbzfuace1gtmw6' );

/** Database username */
define( 'DB_USER', 'uzwvy7dd9roql' );

/** Database password */
define( 'DB_PASSWORD', '5szkqd9udoxp' );

/** Database hostname */
define( 'DB_HOST', '127.0.0.1' );

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
define( 'AUTH_KEY',          'mph&$mvMxU{cI5xL@0eWeJrdSeECV<mh%>Rb}67N/%!Q{,hRFQOBQ.~)iTDmP@|d' );
define( 'SECURE_AUTH_KEY',   '>bAt@gb[06dt?WY9&=+^fQ{uvb5mau(XrH4EO=++q*IkRJrM<u{Vp14`h3S?]G?A' );
define( 'LOGGED_IN_KEY',     '>:IkPSB5G,mYN%~Oar^Se21XS${VB({@ee/6P=BDNYoX.k0(b$o/`ytyu~[f16?n' );
define( 'NONCE_KEY',         '6JmlD`|I8;4,tym5VuQnFyox0Y6M~e!pz|$bVOWLx>_u)@Q3RL>*#p:S{WG1E*LZ' );
define( 'AUTH_SALT',         '!QB}nFc|`9-3+6Y:JrP(/E})U_HN #D=r~~^m5JR#t%R7MB0wZd,c1Da2mN.DXUt' );
define( 'SECURE_AUTH_SALT',  '<E?0k+HTR2%9@5}.n3cpL8*MQaYQx5Jsq6E!@8b9|-SYfN10xWtwyG#-$+J|6MtZ' );
define( 'LOGGED_IN_SALT',    'se#}Lck0c(m1rZi($xh{B20C58m(a8mL!y/gc1t^`jY]hp|a %h_:Yl3ycW#WL^1' );
define( 'NONCE_SALT',        '$6RS u.q}TnZ(s)bvL)!.,`|v;FaWxs 6|C&+O7P.K,?Zl:F6Nc!ztJ7:*BiA-#s' );
define( 'WP_CACHE_KEY_SALT', 'm~D5-Gj#0@ tM4F$Zw_XK~%]X#B2#?6dEjr#:zFOi`S_dbS-7FWc{R`>#k-1fJ:d' );


/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'dvb_';


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

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
@include_once('/var/lib/sec/wp-settings-pre.php'); // Added by SiteGround WordPress management system
require_once ABSPATH . 'wp-settings.php';
@include_once('/var/lib/sec/wp-settings.php'); // Added by SiteGround WordPress management system
