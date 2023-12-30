<?php

namespace DevWael\WpPostsSender\Admin;

// Exit if accessed directly
if ( ! defined( '\ABSPATH' ) ) {
	exit;
}

class AdminOptions {

	/**
	 * Load hooks
	 *
	 * @return void
	 */
	public function load_hooks(): void {
		add_action( 'acf/init', [ $this, 'register_acf_options_page' ] );
		add_filter( 'acf/settings/load_json', [ $this, 'load_acf_fields_json' ] );
	}

	/**
	 * Register ACF options page
	 *
	 * @return void
	 */
	public function register_acf_options_page(): void {
		if ( function_exists( 'acf_add_options_page' ) ) {
			\acf_add_options_page( [
				'page_title' => \esc_html__( 'WP Posts Sender Settings', 'wp-posts-sender' ),
				'menu_title' => \esc_html__( 'WP Posts Sender', 'wp-posts-sender' ),
				'menu_slug'  => 'wp-posts-sender-settings',
				'capability' => 'manage_options',
				'redirect'   => false,
			] );
		}
	}

	/**
	 * Load ACF fields from JSON
	 *
	 * @param array $paths
	 *
	 * @return array
	 */
	public function load_acf_fields_json( array $paths ): array {
		$paths[] = WP_POSTS_SENDER_PLUGIN_DIR . 'acf';

		return $paths;
	}
}