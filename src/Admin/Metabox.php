<?php

namespace DevWael\WpPostsSender\Admin;

// Exit if accessed directly
use DevWael\WpPostsSender\Helpers;

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
                            class="button button-primary"
                            data-nonce="<?php
					        echo esc_attr( wp_create_nonce() ) ?>"
                            data-site-url="<?php
					        echo esc_url( $site_url ); ?>"
                            data-site-name="<?php
					        echo esc_attr( $site_name ); ?>">
						<?php
						echo esc_html( $site_name ); ?>
                    </button>
                </p>
				<?php
			}
		}
	}

}