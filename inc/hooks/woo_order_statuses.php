<?php
// Register new status
function woordf_register_wait_call_order_status() {
	register_post_status( 'wc-files-downloaded', array(
		'label'                     => 'Waiting call',
		'public'                    => true,
		'show_in_admin_status_list' => true,
		'show_in_admin_all_list'    => true,
		'exclude_from_search'       => false,
		'label_count'               => _n_noop( __('Order files downloaded (%s)',WORDF_LANG),__('Order files downloaded (%s)',WORDF_LANG))
	) );
}
// Add custom status to order status list
function woordf_add_wait_call_to_order_statuses( $order_statuses ) {
	$new_order_statuses = array();
	foreach ( $order_statuses as $key => $status ) {
		$new_order_statuses[ $key ] = $status;
		if ( 'wc-on-hold' === $key ) {
			$new_order_statuses['wc-files-downloaded'] = __('Order files downloaded',WORDF_LANG);
		}
	}
	return $new_order_statuses;
}
add_action( 'init', 'woordf_register_wait_call_order_status' );
add_filter( 'wc_order_statuses', 'woordf_add_wait_call_to_order_statuses' );
?>