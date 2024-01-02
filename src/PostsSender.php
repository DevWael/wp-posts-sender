<?php

namespace DevWael\WpPostsSender;

// Exit if accessed directly
if ( ! defined( '\ABSPATH' ) ) {
	exit;
}

class PostsSender {

	/**
	 * Post requests.
	 *
	 * @var array
	 */
	private array $post_requests;

	public function __construct() {
		$this->post_requests = Helpers::post();
	}

	/**
	 * Load hooks
	 *
	 * @return void
	 */
	public function load_hooks(): void {
		add_action( 'wp_ajax_wp_posts_sender', [ $this, 'posts_sender' ] );
	}

	/**
	 * Send posts to another site.
	 *
	 * @return void
	 */
	public function posts_sender() {

		if ( ! $this->has_permission() ) {
			wp_send_json_error( [ 'message' => __( 'You do not have permission to do this action.', 'wp-posts-sender' ) ] );
		}
		if ( ! $this->nonce_passed() ) {
			wp_send_json_error( [ 'message' => __( 'Nonce verification failed.', 'wp-posts-sender' ) ] );
		}

		if ( ! $this->site_url_exists() ) {
			wp_send_json_error( [ 'message' => __( 'Site url is not valid.', 'wp-posts-sender' ) ] );
		}

		if ( ! $this->post_id_exists() ) {
			wp_send_json_error( [ 'message' => __( 'Post id is not valid.', 'wp-posts-sender' ) ] );
		}

		wp_send_json_success( [ 'message' => __( 'Post sent successfully.', 'wp-posts-sender' ) ]);
	}

	/**
	 * Check if the site url is valid.
	 *
	 * @return bool
	 */
	private function site_url_exists(): bool {
		if ( ! isset( $this->post_requests['site_url'] ) ) {
			return false;
		}

		$site_url = $this->post_requests['site_url'];
		if ( ! filter_var( $site_url, FILTER_VALIDATE_URL ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Check if the post id is valid.
	 *
	 * @return bool
	 */
	private function post_id_exists(): bool {
		if ( ! isset( $this->post_requests['post_id'] ) ) {
			return false;
		}

		$post_id = $this->post_requests['post_id'];
		if ( ! is_numeric( $post_id ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Check if the user has permission to send posts.
	 *
	 * @return bool
	 */
	private function has_permission(): bool {
		return current_user_can( 'edit_posts' );
	}

	/**
	 * Check if the nonce passed.
	 *
	 * @return bool
	 */
	private function nonce_passed(): bool {
		if (
			! isset( $this->post_requests['nonce'] )
			|| ! wp_verify_nonce( $this->post_requests['nonce'], 'wp_posts_sender_nonce' )
		) {
			return false;
		}

		return true;
	}

	/**
	 * Check if the link is existing.
	 *
	 * @param string $link Link to check.
	 *
	 * @return bool
	 */
	private function is_link_existing( string $link ): bool {
		$links_array = Helpers::get_acf_field( 'wp_posts_sender_sites', 'option' );
		if ( ! $links_array ) {
			return false;
		}

		foreach ( $links_array as $link_array ) {
			if ( $link_array['site_link'] === $link ) {
				return true;
			}
		}
	}
}