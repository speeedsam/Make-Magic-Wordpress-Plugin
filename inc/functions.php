<?php
/**
 * This is function file mange for globaly
 *
 * @package MakeMagic
 */

/**
 * Insert data into the database.
 *
 * @param string $name The thing name.
 * @package function
 */
function makemagic_insert_data( $name ) {
	global $wpdb;

	$name       = sanitize_text_field( $name );
	$table_name = $wpdb->prefix . 'makemagic_things';

    $result = $wpdb->insert( // phpcs:ignore.
		$table_name,
		array(
			'thing_name' => $name,
		),
		array(
			'%s', // Data format for 'thing_name' column (string).
		)
	);
    if($result){
        return 'Data inserted successfully!';
    }
}

/**
 * Get data from the database.
 *
 * @param int    $page    The current page.
 * @param int    $per_page Items per page.
 * @param string $orderby Order by field.
 * @param string $order   Order direction.
 * @param string $search  Search keyword.
 * @return array Data array.
 */
function makemagic_get_data( $page = 1, $per_page = 10, $orderby = 'id', $order = 'ASC', $search = '' ) {
	global $wpdb;
	$table_name    = $wpdb->prefix . 'makemagic_things';
	$valid_columns = array( 'id', 'thing_name' );
	if ( ! in_array( $orderby, $valid_columns, true ) ) {
		$orderby = 'id';
	}

	$order = strtoupper( $order );
	if ( 'ASC' !== $order && 'DESC' !== $order ) {
		$order = 'ASC';
	}

	$offset = ( $page * $per_page ) - $per_page;
	$query  = "SELECT * FROM {$table_name}"; // phpcs:ignore.
	if ( $search ) {
		$query .= $wpdb->prepare( ' WHERE thing_name LIKE %s', '%' . $wpdb->esc_like( $search ) . '%' );
	}
	$data = $wpdb->get_results( $query . ' ORDER BY id ' . $order . ' LIMIT ' . $offset . ', ' . $per_page, OBJECT ); // phpcs:ignore.

	// Get the total number of items for pagination.
	$total_query = "SELECT COUNT(*) FROM {$table_name}";
	if ( $search ) {
		$total_query .= $wpdb->prepare( ' WHERE thing_name LIKE %s', '%' . $wpdb->esc_like( $search ) . '%' ); // phpcs:ignore.
	}
	$total_items = $wpdb->get_var( $total_query ); // phpcs:ignore.

	return array(
		'data'         => $data,
		'total'        => $total_items,
		'per_page'     => $per_page,
		'current_page' => $page,
	);
}
