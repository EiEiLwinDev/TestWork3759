<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the website, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'testwork' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', '' );

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
define('AUTH_KEY',         '%g`SC5l.z1FBXz(Bgk|ur-n,CDPhm&56g]_uua&eSr|~y?p(2tA:Nl)v6!>AJ0l');
define('SECURE_AUTH_KEY',  '<z%-cSd`3sRK2K[_3HW8+,-`e6vqv|Lq{W-?|j:[yq19jZ:SxlbFQ7y/-bN3W');
define('LOGGED_IN_KEY',    'J0?XV|Mv!hN-A.ygK_s|;]X&GH,A~1(.@w`a`!QJIM $:qG->z|HhX!6?F47Prf5');
define('NONCE_KEY',        '|SV|x|1F~5)5;jJE>mFQdDg7cOp/!a44x-cBc[kQB<ZDa_ot^nNdabb#XIjInY,^');
define('AUTH_SALT',        'dHrW|~&a87qNib2[ F-P|xGBK)_f+5(b;%/0Oq47%7m-$3s/c#O=BSQMMYMvp`');
define('SECURE_AUTH_SALT', '^.SAidD8ZRo@l>ud9O-?&-;As9q=zzdb-UGpmJg:2gb>re^]9-VD<i|eU+!|*4');
define('LOGGED_IN_SALT',   'g!O%)q*t!.]M27+>pGE_D}ok!>k/izv<1*qx6@:6|O%D{ZpI2_xH06%%zav-y1!N');
define('NONCE_SALT',       'PHXEo9pel1vG}f9FiUMb!sl+M<^L.A}@1G2--o7(w@/jjN7i2T=a%=O%=@nG-4Iy');

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'tw_';

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
 * @link https://developer.wordpress.org/advanced-administration/debug/debug-wordpress/
 */
define( 'WP_DEBUG', false );

/* Add any custom values between this line and the "stop editing" line. */

define( 'FS_METHOD', 'direct' );

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
