<?php

add_action( 'init', 'woordf_hooks_init' );

function woordf_hooks_init(){
	add_filter( 'woocommerce_locate_template', 'woordf_woo_menu_rewrites', 10, 3 );
}

function woordf_woo_menu_rewrites($template, $template_name, $template_path ){
	if('order/order-downloads.php' == $template_name ){
		
		$template = WORDF_WOOCOMMERCE_TEMPLATE_PATH . 'order/order-downloads.php';
	
	}
	return $template;
}
add_filter( 'woocommerce_account_menu_items', 'woordf_my_account_menu_items' );
function woordf_my_account_menu_items( $items ) {
    unset($items['downloads']);
    return $items;
}

//endpoints 
add_filter ( 'woocommerce_account_menu_items', 'woordf_account_menu_items', 10 );
	function woordf_account_menu_items( $menu_links ){
		$menu_links = array_slice( $menu_links, 0,3 , true )
			+ array( 'order-downloads' => __("Downloads",WORDF_LANG) )
			+ array_slice( $menu_links, 3, NULL, true );
		return $menu_links;
	}
add_action( 'init', 'woordf_endpoints' );
function woordf_endpoints() {
    $endpoints=woordf_woo_my_account_endpoints();
	foreach($endpoints as $item){
		 add_rewrite_endpoint( $item["endpoint"], EP_PAGES );
	}
}
//views
$endpoints=woordf_woo_my_account_endpoints();

foreach($endpoints as $item){
	if($item['in_woo_navigation']){
		add_action( 'woocommerce_account_'.$item["endpoint"].'_endpoint', function($arguments) use ($item) {
		    include_once WORDF_DIR.'views/'.$item["view"].'.php';
		});	
	}
}

function woordf_admin_user_id(){
    global $wpdb;
    $wp_user_search = $wpdb->get_results("SELECT ID, display_name FROM $wpdb->users ORDER BY ID");

    $admin_id=0;
    foreach ( $wp_user_search as $userid ) {
        $curID = $userid->ID;
        $curuser = get_userdata($curID);
        $user_level = $curuser->user_level;
        if($user_level >= 8){
           $admin_id= $curID;
           break;
        }
    }
    return $admin_id;
}
add_action( 'init', 'woordf_rewrite_rules' );
function woordf_rewrite_rules()
{
	$args = array(
	    'post_type'  => 'page', 
	    'meta_query' => array( 
	        array(
	            'key'   => '_wp_page_template', 
	            'value' => 'embed.php'
	        )
	    )
	);
	$preview_post=get_posts($args);
	if(empty($preview_post)){
			$post = array();
			$post['post_status']   = 'publish';
			$post['post_type']     = 'page'; 
			$post['post_name']     = 'embeded'; 
			$post['post_title']    = __('Puslapis įterpimui', WORDF_LANG);
			$post['post_content']  = '';
			$post['post_excerpt']  = '';
			$post['post_author']   = woordf_admin_user_id();


			$post_id = wp_insert_post( $post );
			update_post_meta( $post_id, '_wp_page_template', 'embed.php');
			
			file_put_contents(dirname(__FILE__).'/testas_empty.txt', var_export($preview_post,true).PHP_EOL, FILE_APPEND | LOCK_EX);
	}

	$preview_post=get_posts($args);
	$post_id=$preview_post[0]->ID;

    add_rewrite_rule(
        '^'.$preview_post[0]->post_name.'/([^/]*)/?',
        'index.php?page_id='.$post_id.'&record_id=$matches[1]',
       'top'
    );	

	


	

}
add_filter('query_vars', 'woordf_menu_query_var' );

function woordf_menu_query_var($vars) {
	$vars[] = 'record_id';
	return $vars;
}
add_filter("woocommerce_get_query_vars", function ($vars) {
    $endpoints=woordf_woo_my_account_endpoints();
    if($endpoints)
	foreach($endpoints as $item){
		 $vars[$item["endpoint"]] = $item["endpoint"];
	}
    return $vars;

});

?>