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
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'dietgirl');

/** MySQL database username */
define('DB_USER', 'root');

/** MySQL database password */
define('DB_PASSWORD', '');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8mb4');

/** The Database Collate type. Don't change this if in doubt. */
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
define('AUTH_KEY',         'Cegnyr#o&BWX|0AGr w1DJ$:MVQ$3grA],O.^>.w.$0r|EDzhnWt93p9<)_{V Y{');
define('SECURE_AUTH_KEY',  'c>G@c13~VPiOc|]v.DokV|7`u]m~ITlW=SFC=(so@]tzlEi4>.Z8(AU+H65rnvEE');
define('LOGGED_IN_KEY',    'erwEQ:H4 jHsR&Yc8~/CKP !J>gMhe]bsn*ryWqQ[Xi]_!.qP)cP8MOw.aC}r&M[');
define('NONCE_KEY',        '-PI65q>sozYHvyT&(POL{hPWVnMQ,f%NsA70._HkK0Y6UNvthmwiP[Y;*55AI??,');
define('AUTH_SALT',        'x(L:.qb,,nu[8lhA[g~!+8[isPV9da/,C`6eVyt}#(n-?TBs-xO@^p$h(?#I j],');
define('SECURE_AUTH_SALT', 'a[:ab>Od.uPU&K0^7TiD%]3V%.rJ!Knlx7{59L9Vt`}m)$K(B476!X4Q1mgN8=U[');
define('LOGGED_IN_SALT',   '%ybBu#dfzgX@_IFzZzjD`olb]oI,&zA!,_3V.`CG(P59%8ls,bZkNf+zp|9*VWdF');
define('NONCE_SALT',       '>43hdQ)CX=?CKy;`_RjS| 2p9Uk=Nz+. )1|3s#`Vm/) 94;Ey$x!W#$!59SW<-R');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the Codex.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
