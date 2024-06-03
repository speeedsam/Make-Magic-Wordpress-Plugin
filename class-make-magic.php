<?php
/**
 * Plugin Name: Make Magic
 * Description: Make magic.
 * Plugin URI: https://example.com
 * Version: 1.0
 * Author: Shah Zobayer Ahmed
 * Author URI: https://example.com
 * License: GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: make-magic
 * Domain Path: /languages
 *
 * @package Make_Magic
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

require_once __DIR__ . '/vendor/autoload.php';


/**
 * Main class Make_Magic
 */
final class Make_Magic {

	/**
	 * Plugin Version
	 *
	 * @since 1.0.0
	 * @var string The plugin version.
	 */
	const VERSION = '1.0';

	/**
	 * Minimum PHP Version
	 *
	 * @since 1.2.0
	 * @var string Minimum PHP version required to run the plugin.
	 */
	const MINIMUM_PHP_VERSION = '7.4';

	/**
	 * Constructor
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function __construct() {
        // die();
		/**
		 * Define constants.
		 */
		$this->define_constants();

		register_activation_hook( MAKEMAGIC__FILE__, [$this, 'activate' ]);

		// Load translation.
		add_action( 'init', array( $this, 'i18n' ) );

		// Init Plugin.
		add_action( 'plugins_loaded', array( $this, 'init' ) );
	}

	/**
	 * Load Textdomain
	 *
	 * Load plugin localization files.
	 * Fired by `init` action hook.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function i18n() {
		load_plugin_textdomain( 'make-magic' );
	}

	/**
	 * Initialize the plugin
	 * Fired by `plugins_loaded` action hook.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function init() {

		// Checking admin or frontend.
		if ( is_admin() ) {
			// Enqueue all admin styles and scripts.
		} else {
			new MakeMagic\AdminPanel();
			// Manage all frontend functionality.
			new MakeMagic\FrontendPanel();
		}
	}

	/**
	 * Define the required plugin constants
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function define_constants() {
		define( 'MAKEMAGIC_VERSION', self::VERSION );
		define( 'MAKEMAGIC__FILE__', __FILE__ );
		define( 'MAKEMAGIC_PLUGIN_BASE', plugin_basename( MAKEMAGIC__FILE__ ) );
		define( 'MAKEMAGIC_PATH', plugin_dir_path( MAKEMAGIC__FILE__ ) );
		define( 'MAKEMAGIC_ASSETS_PATH', MAKEMAGIC_PATH . 'assets/' );
		define( 'MAKEMAGIC_MODULES_PATH', MAKEMAGIC_PATH . 'modules/' );
		define( 'MAKEMAGIC_URL', plugins_url( '/', MAKEMAGIC__FILE__ ) );
		define( 'MAKEMAGIC_ASSETS_URL', MAKEMAGIC_URL . 'assets/' );
		define( 'MAKEMAGIC_MODULES_URL', MAKEMAGIC_URL . 'modules/' );
	}

	/**
	 * Do stuff upon plugin activation
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function activate() {
		$installer = new MakeMagic\Installer();
		$installer->run();
	}

	/**
	 * Admin notice
	 *
	 * Warning when the site doesn't have a minimum required PHP version.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function admin_notice_minimum_php_version() {
		// Unset 'activate' from $_GET if set.
		if ( isset( $_GET['activate'] ) ) {
			unset( $_GET['activate'] );
		}

		// Check nonce for activation.
		if ( isset( $_GET['_wpnonce'] ) ) {
			$nonce = sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) );
			if ( ! wp_verify_nonce( $nonce, 'plugin-activation' ) ) {
				return;
			}
		}

		// Construct the message with escaped variables.
		$message = sprintf(
			/* translators: 1: Plugin name 2: PHP 3: Required PHP version */
			esc_html__( '"%1$s" requires "%2$s" version %3$s or greater.', 'make-magic' ),
			'<strong>' . esc_html__( 'Make Magic Plugin', 'make-magic' ) . '</strong>',
			'<strong>' . esc_html__( 'PHP', 'make-magic' ) . '</strong>',
			self::MINIMUM_PHP_VERSION
		);

		// Display the notice.
		printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', esc_html( $message ) );
	}
}

// Instantiate Make_Magic.
new Make_Magic();
