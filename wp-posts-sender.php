<?php
/**
 * Plugin Name: WP Posts Sender
 * Plugin URI: https://github.com/DevWael/wp-posts-sender
 * Description: Send posts to another WP site
 * Version: 1.0
 * Author: Ahmad Wael
 * Author URI: https://www.bbioon.com
 * License: GPL-3.0+
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: wp-posts-sender
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'WP_POSTS_SENDER_VERSION', '1.0' );
define( 'WP_POSTS_SENDER_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'WP_POSTS_SENDER_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

require_once WP_POSTS_SENDER_PLUGIN_DIR . 'vendor/autoload.php';

use DevWael\WpPostsSender\Main;

Main::getInstance()->init();