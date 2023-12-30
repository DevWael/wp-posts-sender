<?php

namespace DevWael\WpPostsSender\Admin;

use DevWael\WpPostsSender\Helpers;

// Exit if accessed directly
if ( ! defined( '\ABSPATH' ) ) {
	exit;
}

class Metabox {

	/**
	 * Load hooks
	 *
	 * @return void
	 */
	public function load_hooks(): void {
		add_action( 'add_meta_boxes', [ $this, 'add_meta_boxes' ] );
		add_action( 'save_post', [ $this, 'save_post' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'load_admin_js' ] );
	}

	/**
	 * Load admin js
	 *
	 * @return void
	 */
	public function load_admin_js(): void {
		$screen = get_current_screen();
		if ( $screen && ! in_array( $screen->post_type, Helpers::supported_post_types(), true ) ) {
			return;
		}
		wp_enqueue_script(
			'wp-posts-sender-admin-js',
			WP_POSTS_SENDER_PLUGIN_URL . 'assets/js/admin.js',
			[ 'jquery' ],
			WP_POSTS_SENDER_VERSION,
			true
		);

		wp_localize_script(
			'wp-posts-sender-admin-js',
			'wp_posts_sender',
			[
				'ajax_url' => admin_url( 'admin-ajax.php' ),
			]
		);
	}

	/**
	 * Add meta boxes
	 *
	 * @return void
	 */
	public function add_meta_boxes(): void {
		$supported_post_types = Helpers::supported_post_types();
		if ( have_rows( 'wp_posts_sender_sites', 'option' ) ) {
			foreach ( $supported_post_types as $post_type ) {
				add_meta_box(
					'wp-posts-sender',
					\esc_html__( 'WP Posts Sender', 'wp-posts-sender' ),
					[ $this, 'render_meta_box' ],
					$post_type,
					'side',
					'high'
				);
			}
		}
	}

	/**
	 * Render meta box
	 *
	 * @param \WP_Post $post
	 *
	 * @return void
	 */
	public function render_meta_box( \WP_Post $post ): void {
		if ( have_rows( 'wp_posts_sender_sites', 'option' ) ) {
			?>
            <p class="description">
				<?php
				esc_html_e(
					'Click on the website you want to copy this post to, make sure to save the post before coping.',
					'wp-posts-sender'
				); ?>
            </p>
            <br>
			<?php
			while ( have_rows( 'wp_posts_sender_sites', 'option' ) ) {
				the_row();
				$site_name = get_sub_field( 'site_name' );
				$site_url  = get_sub_field( 'site_link' );
				?>
                <p>
                    <button type="button"
                            class="button button-primary wp-posts-sender-button"
                            data-nonce="<?php
					        echo esc_attr( wp_create_nonce( 'wp_posts_sender_nonce' ) ) ?>"
                            data-site-url="<?php
					        echo esc_url( $site_url ); ?>"
                            data-post-id="<?php
					        echo esc_attr( $post->ID ) ?>">
						<?php
						echo esc_html( $site_name ); ?>
                    </button>
                </p>
				<?php
			}
		}
	}

}