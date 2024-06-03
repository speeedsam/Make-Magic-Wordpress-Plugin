<?php
/**
 * Admin Panel Class
 *
 * This class manages all admin-related functionality.
 *
 * @package MakeMagic
 */

namespace MakeMagic;

/**
 * API Class call here.
 */
use MakeMagic\Frontend\API;

/**
 * AdminPanel class used for managing
 * all Admin-related functionality.
 *
 * @package MakeMagic
 */
class FrontendPanel {

	/**
	 * Table name
	 *
	 * @var string $table_name
	 */
	private $table_name;

	/**
	 * Constructor
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		global $wpdb;
		new Frontend\FrontendEnqueue();
		// Ensure the REST API routes are registered.
		if ( class_exists( 'MakeMagic\Frontend\API' ) ) {
			new API();
		}
		$this->table_name = $wpdb->prefix . 'makemagic_things';
		add_action( 'init', array( $this, 'makemagic_plugin_init' ) );
	}

	/**
	 * Initialize the plugin.
	 */
	public function makemagic_plugin_init() {
		add_shortcode( 'makemagic', array( $this, 'makemagic_shortcode' ) );
		add_shortcode( 'makemagic_list', array( $this, 'makemagic_shortcode_list' ) );
	}

	/**
	 * Shortcode for form submission.
	 */
	public function makemagic_shortcode() {
		if ( isset( $_POST['_nonce'] ) && wp_verify_nonce( sanitize_key( $_POST['_nonce'] ), 'makemagic_nonce' ) ) {
			if ( isset( $_POST['thing_name'] ) ) {
				$thing_name_sanitized = sanitize_text_field( wp_unslash( $_POST['thing_name'] ) ); // Unslash the input.

				makemagic_insert_data( $thing_name_sanitized ); // Insert the sanitized data into the database.
			}
		}

		ob_start();
		?>
		<form method="POST">
			<input type="hidden" name="_nonce" value="<?php echo esc_attr( wp_create_nonce( 'makemagic_nonce' ) ); ?>">
			<input type="text" name="thing_name" required>
			<input type="submit" value="Submit">
		</form>
		<?php
		return ob_get_clean();
	}

	/**
	 * Shortcode to display a list.
	 *
	 * @param array $atts Shortcode attributes.
	 * @return string Output HTML.
	 */
	public function makemagic_shortcode_list( $atts ) {
		$search = '';
		$atts   = shortcode_atts(
			array(
				'search'   => '',
				'per_page' => 10,
				'orderby'  => 'id',
				'order'    => 'ASC',
			),
			$atts
		);
		if ( isset( $_POST['makemagic_search_nonce_field'] ) && wp_verify_nonce( wp_unslash( sanitize_text_field( $_POST['makemagic_search_nonce_field'] ) ), 'makemagic_search_nonce' ) ) { // phpcs:ignore.
			// Process the form data here.
			$search = isset( $_POST['search'] ) ? sanitize_text_field( wp_unslash( $_POST['search'] ) ) : $atts['search'];
		}
		$per_page     = $atts['per_page'];
		$orderby      = $atts['orderby'];
		$order        = $atts['order'];
		$current_page = isset( $_GET['mpaged'] ) ? absint( $_GET['mpaged'] ) : 1;

		$result = makemagic_get_data( $current_page, $per_page, $orderby, $order, $search );

		$data         = $result['data'];
		$total        = $result['total'];
		$per_page     = $result['per_page'];
		$current_page = $result['current_page'];

		ob_start();
		?>
		<form method="GET">
			<?php wp_nonce_field( 'makemagic_search_nonce', 'makemagic_search_nonce_field' ); ?>
			<input type="text" name="search" value="<?php echo esc_attr( $search ); ?>">
			<input type="submit" value="Search">
		</form>
		<ul>
			<?php foreach ( $data as $item ) : ?>
				<li><?php echo esc_html( $item->thing_name ); ?></li>
			<?php endforeach; ?>
		</ul>
		<div class="pagination">
			<?php
				/*
				* Here is the loop.
				*/
				echo wp_kses(
					paginate_links(
						array(
							'base'      => add_query_arg( 'mpaged', '%#%' ),
							'format'    => '',
							'prev_text' => __( '&laquo;' ),
							'next_text' => __( '&raquo;' ),
							'total'     => ceil( $total / $per_page ),
							'current'   => $current_page,
						)
					),
					array(
						'a'    => array(
							'href'  => array(),
							'class' => array(),
						),
						'span' => array(
							'class'        => array(),
							'aria-current' => array(),
						),
					)
				);

			?>
		</div>
		<?php
		return ob_get_clean();
	}
}
