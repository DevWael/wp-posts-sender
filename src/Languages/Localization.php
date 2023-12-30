<?php

namespace DevWael\WpPostsSender\Languages;

class Localization {

	/**
	 * Load hooks
	 *
	 * @return void
	 */
	public function load_hooks(): void {
		add_action( 'plugins_loaded', [ $this, 'load_text_domain' ] );
	}

	/**
	 * Load text domain
	 *
	 * @return void
	 */
	public function load_text_domain(): void {
		load_plugin_textdomain(
			'wp-posts-sender',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);
	}
}