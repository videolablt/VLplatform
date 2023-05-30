<?php


function add_order_item_meta($item_id, $values) {
	$meta_fields=array('woordf_product_type','woordf_product_vip');
	foreach($meta_fields as $field){
	    $id=$values['data']->get_id();
		$value=woordf_get_meta($id,$field,''); 
		if($value!='') woocommerce_add_order_item_meta($item_id, $field, array($value));
	}


}
add_action('woocommerce_add_order_item_meta', 'add_order_item_meta', 10, 2);

add_action( 'woocommerce_after_order_itemmeta', 'display_admin_order_item_custom_button', 10, 3 );
function display_admin_order_item_custom_button( $item_id, $item, $product ){
    // Only "line" items and backend order pages
    if( ! ( is_admin() && $item->is_type('line_item') ) )
        return;

    $woordf_product_type = $item->get_meta('woordf_product_type');
    $woordf_product_vip = $item->get_meta('woordf_product_vip');

    if( ! empty($woordf_product_type) ) {

        woordf_admin_order_item_output_button($woordf_product_type[0],$woordf_product_vip[0]);
    }
}
?>