<?php

namespace DevWael\WpPostsSender;

class Main {

	/**
	 * @var null singleton
	 */
	private static $instance = null;

	/**
	 * Get instance
	 *
	 * @return self|null
	 */
	public static function getInstance() {
		if ( self::$instance === null ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Constructor
	 */
	private function __construct() {

	}

	/**
	 * Init
	 *
	 * @return void
	 */
	public function init(): void {
	}
}