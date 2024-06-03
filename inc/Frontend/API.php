<?php
/**
 * API Class
 *
 * This class manages all API functionality.
 *
 * @package MakeMagic
 */

namespace MakeMagic\Frontend;

use WP_REST_Request;
use WP_REST_Response;
use WP_Error;

/**
 * API class.
 */
class API {
	/**
	 * Constructor method for the API class.
	 */
	public function __construct() {
		// Register REST API endpoints.
		add_action( 'rest_api_init', array( $this, 'register_makemagic_api_routes' ) );
	}

	/**
	 * Here in register all api end point.
	 */
	public function register_makemagic_api_routes() {
		// Endpoint for inserting data.
		register_rest_route(
			'makemagic/v1',
			'/insert',
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'makemagic_insert_data' ),
				'permission_callback' => '__return_true',
			)
		);

		// Endpoint for retrieving data.
		register_rest_route(
			'makemagic/v1',
			'/get',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'makemagic_get' ),
				'permission_callback' => '__return_true',
				'args'                => array(
					'page'     => array(
						'required'          => false,
						'default'           => 1,
						'validate_callback' => function ( $value ) {
							return is_numeric( $value );
						},
					),
					'per_page' => array(
						'required'          => false,
						'default'           => 10,
						'validate_callback' => function ( $value ) {
							return is_numeric( $value );
						},
					),
					'orderby'  => array(
						'required'          => false,
						'default'           => 'id',
						'sanitize_callback' => 'sanitize_text_field',
					),
					'order'    => array(
						'required'          => false,
						'default'           => 'ASC',
						'sanitize_callback' => 'sanitize_text_field',
					),
					'search'   => array(
						'required'          => false,
						'default'           => '',
						'sanitize_callback' => 'sanitize_text_field',
					),
				),
			)
		);
	}

	/**
	 * Callback function for inserting data to the table via REST API.
	 *
	 * @param WP_REST_Request $request The request object.
	 * @return WP_REST_Response Response object.
	 */
	public function makemagic_insert_data( WP_REST_Request $request ) {
		$name = $request->get_param( 'thing_name' );

		if ( empty( $name ) ) {
			return new WP_Error( 'empty_name', 'Name cannot be empty', array( 'status' => 400 ) );
		}

		makemagic_insert_data( $name );

		return new WP_REST_Response( 'Data inserted successfully', 200 );
	}

	/**
	 * Callback function for getting data from the table via REST API.
	 *
	 * @param WP_REST_Request $request The request object.
	 * @return WP_REST_Response Response object.
	 */
	public function makemagic_get( WP_REST_Request $request ) {
		$page     = $request->get_param( 'page' );
		$per_page = $request->get_param( 'per_page' );
		$orderby  = $request->get_param( 'orderby' );
		$order    = $request->get_param( 'order' );
		$search   = $request->get_param( 'search' );

		$data = makemagic_get_data( $page, $per_page, $orderby, $order, $search );

		return new WP_REST_Response( $data, 200 );
	}
}
