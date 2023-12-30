<?php

namespace DevWael\WpPostsSender;

// Exit if accessed directly
if ( ! defined( '\ABSPATH' ) ) {
	exit;
}


class Helpers {

	/**
	 * Get ACF field
	 *
	 * @param string $selector
	 * @param mixed  $post_id
	 * @param bool   $format_value
	 *
	 * @return mixed
	 */
	public static function get_acf_field( string $selector, $post_id = false, bool $format_value = true ) {
		return function_exists( 'get_field' ) ? get_field( $selector, $post_id, $format_value ) : false;
	}

	/**
	 * Get supported post types.
	 *
	 * @return array
	 */
	public static function supported_post_types(): array {
		/**
		 * Filter supported post-types.
		 *
		 * @param array $post_types Supported post-types.
		 *
		 * @ssince 1.0.0
		 */
		return \apply_filters( 'wp_posts_sender_supported_post_types', [ 'post', 'product' ] );
	}

	/**
	 * Get $_GET request parameters.
	 *
	 * @return array $_GET request parameters
	 */
	public static function get(): array {
		// phpcs:disable
		return $_GET;
		// phpcs:enable
	}

	/**
	 * Get $_POST request parameters.
	 *
	 * @return array $_POST request parameters
	 */
	public static function post(): array {
		// phpcs:disable
		return $_POST;
		// phpcs:enable
	}
}