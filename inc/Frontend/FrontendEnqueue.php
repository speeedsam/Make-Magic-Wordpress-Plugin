<?php
/**
 * Frontend Enqueue Class
 *
 * @package MakeMagic
 */

namespace MakeMagic\Frontend;

/**
 * Class FrontendEnqueue
 *
 * Handles enqueuing of front-end scripts and styles.
 */
class FrontendEnqueue {

	/**
	 * Constructor.
	 */
	public function __construct() {
		/**
		 * Enqueue method for scripts and styles
		 *
		 * @load_make_magic_assets
		 */
		add_action( 'wp_enqueue_scripts', array( $this, 'load_make_magic_assets' ) );
	}

	/**
	 * Enqueues all front-end scripts and styles.
	 *
	 * @return void
	 */
	public function load_make_magic_assets() {
		/**
		 * Enqueue All Styles
		 */
		wp_enqueue_style( 'makemagic-style', MAKEMAGIC_ASSETS_URL . 'frontend/css/makemagic-style.css', array(), MAKEMAGIC_VERSION, 'all' );
	}
}
