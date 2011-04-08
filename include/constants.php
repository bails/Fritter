<?php
/*
 * 	constants.php
 *
 * this file is intended to group all constants to make it easier for
 * the site administrator to tweak the login script
 *
 */

/*
 * Database constants - these constants are required in order
 * for there to be a successful connection to the MySQL database.
 * Make sure the information is correct.
 */
define("DB_SERVER", "localhost");
define("DB_USER", "root");
define("DB_PASS", "abc");
define("DB_NAME", "fritterapp");

/*
 * Database Table cosntants - these constants hold the names of
 * all the database tables used in the script
 */
define("TBL_USERS", "users");
define("TBL_ACTIVE_USERS", "active_users");
define("TBL_ACTIVE_GUESTS", "active_guests");
define("TBL_BANNED_USERS", "banned_users");
define("TBL_USER_IMAGES", "user_images");
define("TBL_LOLS", "lols");
/*
 * Special Names and Level Constants
 * The admin page will only be accessible to the user
 * with the admin name and also to those users at the
 * admin user level. Feel free to change the names and
 * level cosntants as you see fit, you may also add
 * additional level specifications.
 * Levels bust be digits between 0-9.
 */
define("ADMIN_NAME", "admin");
define("GUEST_NAME", "Guest");
define("ADMIN_LEVEL", 9);
define("USER_LEVEL", 1);
define("GUEST_LEVEL", 0);

/**
 * This boolean cosntant controls whether or not the
 * script keeps track of active users and active
 * guests who are visitign the siet.
 */
define("TRACK_VISITORS", true);

/**
 * Timeout Constants
 * these constants refer to the maximum amount of time (in minutes)
 * after their last page fresh that a user and guest
 * are still considered active visitors.
 */
define("USER_TIMEOUT", 10);
define("GUEST_TIMEOUT", 5);

/**
 * Cookie constants
 * these are the parameters to the setcookie function call,
 * change them if necessary to fit your website. if you need
 * help, visit www.php.net for moere info
 * <http://www.php.net/manual/en/function.setcookie.php>
 */
define("COOKIE_EXPIRE", 60*60*24*10);	//100 days by default
define("COOKIE_PATH", "/");	//available in whole domain

/**
 * Email cosntants
 * these specify what goes in the field in the emails that
 * the script sends to users, and whether to send a
 * welcome email to newly registered users.
 */
define("EMAIL_FROM_NAME","fritter");
define("EMAIL_FROM_ADDR","fritter@fritter.com");
define("EMAIL_WELCOME", false);

/**
 * This cosntant forces all users to have
 * lowercase usernames, capital letters are
 * converted automatically.
 */
define("ALL_LOWERCASE", false);
?>