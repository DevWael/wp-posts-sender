<?php

namespace DevWael\WpPostsSender;

// Exit if accessed directly
use DevWael\WpPostsSender\Admin\AdminOptions;
use DevWael\WpPostsSender\Languages\Localization;

if ( ! defined( '\ABSPATH' ) ) {
	exit;
}

class Main {

	/**
	 * @var null singleton
	 */
	private static $instance = null;

	/**
	 * @var AdminOptions
	 */
	private AdminOptions $admin_options;

	/**
	 * @var Admin\Metabox
	 */
	private Admin\Metabox $meta_box;

	/**
	 * @var PostsSender
	 */
	private PostsSender $posts_sender;

	/**
	 * @var Localization
	 */
	private Localization $localization;

	/**
	 * Get instance
	 *
	 * @return self|null
	 */
	public static function get_instance() {
		if ( self::$instance === null ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Constructor
	 */
	private function __construct() {
		$this->admin_options = new AdminOptions();
		$this->meta_box      = new Admin\Metabox();
		$this->posts_sender  = new PostsSender();
		$this->localization  = new Localization();
	}

	/**
	 * Init
	 *
	 * @return void
	 */
	public function init(): void {
		$this->admin_options->load_hooks();
		$this->meta_box->load_hooks();
		$this->posts_sender->load_hooks();
		$this->localization->load_hooks();
	}
}