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

		if ( ! $this->is_link_existing( $this->post_requests['site_url'] ) ) {
			wp_send_json_error( [ 'message' => __( 'Site url is not existing.', 'wp-posts-sender' ) ] );
		}

		$response = $this->send_post_copy( sanitize_text_field( $this->post_requests['post_id'] ), sanitize_url( $this->post_requests['site_url'] ) );

		if ( ! $response || is_wp_error( $response ) || wp_remote_retrieve_response_code( $response ) !== 200 ) {
			wp_send_json_error( [ 'message' => __( 'Something went wrong.', 'wp-posts-sender' ) ] );
		}

		wp_send_json_success( [ 'message' => __( 'Post sent successfully.', 'wp-posts-sender' ), $response ] );
	}

	/**
	 * Send post copy to another site.
	 *
	 * @param $post_id
	 * @param $site_url
	 *
	 * @return array|false|\WP_Error WP_Error on failure. The response object on success.
	 */
	public function send_post_copy( $post_id, $site_url ) {
		$post = get_post( $post_id );
		if ( ! $post ) {
			return false;
		}

		// Prepare post-data.
		$post_data = [
			'post_title'      => $post->post_title,
			'post_content'    => $post->post_content,
			'post_status'     => $post->post_status,
			'post_type'       => $post->post_type,
			'post_excerpt'    => $post->post_excerpt,
			'post_image'      => get_the_post_thumbnail_url( $post_id ),
			'post_url'        => get_permalink( $post_id ),
			'post_date'       => $post->post_date,
			'post_taxonomies' => wp_get_post_terms( $post_id, get_object_taxonomies( $post->post_type ) ),
			'post_acf_fields' => Helpers::get_acf_post_fields( $post_id ),
			'post_author'       => $post->post_author,
		];

		// Send post data to the remote site.
		$site_url = untrailingslashit( $site_url ) . '/wp-json/' . Helpers::remote_site_rest_endpoint();

		return wp_remote_post(
			$site_url,
			[
				'body'      => [
					'post_data'   => (array) $post_data,
					'encrypt_key' => sanitize_text_field( Helpers::get_acf_field( 'wp_posts_sender_encryption_key', 'option' ) ),
				],
			]
		);
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