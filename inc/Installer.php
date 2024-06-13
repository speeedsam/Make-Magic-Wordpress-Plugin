<?php
/**
 * Installer Class
 *
 * This class manages plugin installation tasks.
 *
 * @package MakeMagic
 */

namespace MakeMagic;

/**
 * Installer class used for managing
 * all Admin-related functionality.
 *
 * @package Installer
 */
class Installer {

	/**
	 * Constructor
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
	}

	/**
	 * Run function
	 *
	 * This function runs when the plugin is activated.
	 *
	 * @return void
	 */
	public function run() {
		$this->add_version();
		$this->create_makemagic_thing_table();
	}

	/**
	 * Add plugin version
	 *
	 * @return void
	 */
	public function add_version() {
		$installed = get_option( 'make_magic_installed' );
		if ( ! $installed ) {
			update_option( 'make_magic_installed', time() );
		}
		update_option( 'make_magix_version', MAKEMAGIC_VERSION );
	}

	/**
	 * Create product table
	 *
	 * @return void
	 */
	public function create_makemagic_thing_table() {
		global $wpdb;
		$thing_table = $wpdb->prefix . 'makemagic_things';
		if ( ! $this->is_table_exists( $thing_table ) ) {
			// Check if the table exists before creating it.
			$charset_collate = $wpdb->get_charset_collate();

			$sql_thing_table = "CREATE TABLE $thing_table (
                id mediumint(9) NOT NULL AUTO_INCREMENT,
                thing_name varchar(255) NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (id)
            ) $charset_collate;";

			if ( ! function_exists( 'dbDelta' ) ) {
				require_once ABSPATH . 'wp-admin/includes/upgrade.php';
			}
			dbDelta( $sql_thing_table );
		} else {
			wp_die( 'Table Already exists!' );
		}
	}

	/**
	 * Check if table exists
	 *
	 * @param string $table_name The name of the table.
	 * @return bool
	 */
	public function is_table_exists( $table_name ) {
		global $wpdb;

		// Generate the table name including the prefix.
		$get_table = $wpdb->prefix . $table_name;

		// Check if the table name exists in the cache.
		$table_exists = wp_cache_get( $get_table, 'makemagic_things' );

		// If not found in cache, check the database.
		if ( false === $table_exists ) {
			$sql = $wpdb->prepare( 'SHOW TABLES LIKE %s', '%' . $wpdb->esc_like( $get_table ) . '%' );

			// Execute the SQL query.
			$table_exists = $wpdb->get_var( $sql ); // phpcs:ignore.

			// Cache the result.
			wp_cache_set( $get_table, $table_exists, 'makemagic_things' );
		}

		// Return true if the table exists, false otherwise.
		return (bool) $table_exists;
	}
}
