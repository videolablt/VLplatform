<?php
//user meta 


add_action( 'show_user_profile', 'woordf_extra_register_fields_admin' );
add_action( 'edit_user_profile', 'woordf_extra_register_fields_admin' );

function woordf_extra_register_fields_admin( $user ) {
	$administrator=woordf_current_user_role('administrator');
	$orders_viewer_profile=woordf_current_user_role('orders_viewer', $user->ID);
	if($administrator && $orders_viewer_profile){
			$woordf_manager_customers = maybe_unserialize(get_the_author_meta( 'woordf_manager_customers', $user->ID ));
			$woordf_manager_customers=($woordf_manager_customers)?$woordf_manager_customers : array();

	        $args = array(
	        	'role' => 'customer'
	    	);
	    	$users = get_users( $args );
		?>
		<h3><?php _e("Assign customers for this order manager",WORDF_LANG);?></h3>
	 	<table class="form-table">
        <tr>
            <th><label><?php _e("Customers",WORDF_LANG);?></label></th>
            <td>
<?php
foreach($users as $user){
	?><input type="checkbox" value="<?php echo $user->id;?>" name="woordf_manager_customers[]"<?php echo (in_array($user->id,$woordf_manager_customers))?' checked':'';?> /><label style="padding:4px 12px 4px 2px;"><?php echo $user->display_name;?></label><?php
}
?>
	
            </td>
        </tr>
		</table><?php
	}	
}
//save
add_action( 'edit_user_profile_update', 'woordf_update_profile_fields' );
function woordf_update_profile_fields( $user_id ) {
	$administrator=woordf_current_user_role('administrator');
	$orders_viewer_profile=woordf_current_user_role('orders_viewer', $user_id);
	if($administrator && $orders_viewer_profile){
		if ( isset($_POST['woordf_manager_customers'])) {
			update_user_meta( $user_id, 'woordf_manager_customers', (array)$_POST['woordf_manager_customers'] );
		}
		else update_user_meta( $user_id, 'woordf_manager_customers', (array)array() );
	}

}

//filters 
add_action( 'restrict_manage_posts', 'woordf_display_admin_shop_order_language_filter' );
function woordf_display_admin_shop_order_language_filter(){
    global $pagenow, $post_type;

    if( 'shop_order' === $post_type && 'edit.php' === $pagenow ) {

        $items = array();
        $args = array(
        	'role' => 'orders_viewer'
    	);
    	$users = get_users( $args );
    	foreach($users as $user) $items[]=array('id'=>$user->id,'name'=>$user->display_name);
        $current   = isset($_GET['filter_om_id'])? (int)$_GET['filter_om_id'] : 0;
        echo '<select name="filter_om_id">
        <option value="">' . __('Filter By Orders manager',WORDF_LANG) . '</option>';
        
        foreach ( $items as $item ) {
            printf( '<option value="%s"%s>%s</option>', $item['id'], 
                (int)$item['id']=== $current ? '" selected' : '', $item['name'] );
        }
        echo '</select>';
    }
}

add_action( 'pre_get_posts', 'woordf_process_admin_shop_order_language_filter' );
function woordf_process_admin_shop_order_language_filter( $query ) {
    global $pagenow;

    if ( $query->is_admin && $pagenow == 'edit.php' && isset( $_GET['filter_om_id'] ) 
        && $_GET['filter_om_id'] != '' && $_GET['post_type'] == 'shop_order' ) {

		$woordf_manager_customers = maybe_unserialize(get_the_author_meta( 'woordf_manager_customers', (int)$_GET['filter_om_id']));
		$woordf_manager_customers=($woordf_manager_customers)?$woordf_manager_customers : array();
		if(!empty($woordf_manager_customers)){
			$query->set( 'meta_query', array(
				array(
					'key'     => '_customer_user',
					'value'   => $woordf_manager_customers,
					'compare' => 'IN',
				)
			) );
		}
        
    }
}

?>