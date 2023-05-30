<?php
/**
 * Plugin Name: Woocommerce Order files
 * Plugin URI: #
 * Description: Extends woocommerce functionality, allows attach files to order
 * Author: Nerijus K.
 * Author URI: #
 * Version: 1.1.2
 * Text Domain: woocommerce_order_files
 * Domain Path: /languages/
 * License: All rights reserved: Videolab comapny
 * License #
 * Slug: woocommerce_order_files
 */
global $wpdb;
$upload_dir = wp_upload_dir();
define( 'WORDF_URL', plugin_dir_url( __FILE__ ) );
define( 'WORDF_DIR', plugin_dir_path( __FILE__ ) );
define('WORDF_LANG',"woocommerce_order_files");
define('WORDF_GET_VAR',"oid");
define('WORDF_PREFIX',"woordf");
define('WORDF_PX',"woordf_");
define('WORDF_UPLOADS_FOLDER',$upload_dir['basedir']."/order_uploads/");
define('WORDF_UPLOADS_FOLDER_URL',$upload_dir['baseurl']."/order_uploads/");
define('WORDF_WOOCOMMERCE_TEMPLATE_PATH',plugin_dir_path( __FILE__ ) . 'woocommerce/');


define('WORDF_ADMIN_PAGE_SLUG',"woordf_records");
define('WORDF_OPTIONS_PAGE_SLUG',"woordf_options");
define('WORDF_ADMIN_LOGS_PAGE_SLUG',"woordf_logs");
define('WORDF_ADMIN_NEW_PAGE_SLUG',"woordf_new");
define('WORDF_ADMIN_CATEGORIES_PAGE_SLUG',"woordf_categories");
define('WORDF_ADMIN_PAGE',"Užsakymų failai");
define('WORDF_WP_ADMIN_PAGE_SLUG',"uzsakymu-failai");
define('WORDF_VERSION',"1.0.0");

//db tables
define('WORDF_DB_PREFIX',$wpdb->prefix."worders_");
define('WORDF_UPLOADS_TABLE',WORDF_DB_PREFIX."records");
define('WORDF_LOGS_TABLE',WORDF_DB_PREFIX."logs");
define('WORDF_CATEGORIES_TABLE',WORDF_DB_PREFIX."categories");

include_once WORDF_DIR.'/modules/db_module.php';
include_once WORDF_DIR.'/modules/db_records_module.php';
include_once WORDF_DIR.'/modules/db_logs_module.php';
include_once WORDF_DIR.'/modules/db_categories_module.php';

//webhooks



//constants, values 

include_once WORDF_DIR.'/inc/constants.php';

//functions 

include_once WORDF_DIR.'/inc/functions.php';

//ajax 

include_once WORDF_DIR.'/inc/ajax.php';

//hooks 

include_once WORDF_DIR.'/inc/hooks/woo_my_account_menu.php';

//product metaboxes 
include_once WORDF_DIR.'/inc/hooks/product_metaboxes.php';


//order items admin

include_once WORDF_DIR.'/inc/hooks/woo_admin_order_items.php';

//cron jobs 

include_once WORDF_DIR.'/inc/hooks/cron_jobs.php';

//order statuses 

include_once WORDF_DIR.'/inc/hooks/woo_order_statuses.php';

//order filters 

include_once WORDF_DIR.'/inc/hooks/woo_order_filters.php';

//icons 

include_once WORDF_DIR.'/inc/icons.php';

//template

include_once WORDF_DIR.'inc/templater.php';

//directory handler module 

include_once WORDF_DIR.'modules/directory_handler.php';

include_once WORDF_DIR.'modules/files_handler.php';

class woocommerce_woordf_class
{
    public function __construct() {

		if ( in_array('woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option('active_plugins')) ) || is_plugin_active_for_network( 'woocommerce/woocommerce.php' )) {
	    	add_action('plugins_loaded', array( &$this,'set_language'));
			register_activation_hook(__FILE__, array(&$this, 'install' ));
			add_action('wp_enqueue_scripts', array(&$this,'frontend_scripts'),20);

			if(is_admin()){
				//menu item
				add_action('admin_menu', array($this, 'add_menu_items'));
				//backend scripts	
				add_action('admin_enqueue_scripts', array( &$this, 'backend_scripts' ));
				add_action('admin_init', array(&$this,'options_settings') );

			}
		} else {
			add_action( 'admin_notices', array(&$this, 'admin_notice') );
		}	
    	
    	

    }
    public function options_settings(){
    	include_once WORDF_DIR.'/modules/admin_options_module.php';
    	$admin_options_fields=woordf_admin_options_fields();
    	$admin_options=new woordfAdmin_options_module($admin_options_fields);
    	$fields=$admin_options->get_fields_keys();

    	foreach($fields as $field) register_setting( 'woordf_options', $field);
	}
	//modals
    public function my_acc_modals(){
    	include_once WORDF_DIR.'views/modals.php';
    	include_once WORDF_DIR.'/views/parts/photoswipe.php';
	}
    public function admin_modals(){
    	include_once WORDF_DIR.'/views/parts/photoswipe_min.php';
	}

	public static function admin_notice() {
		global $pagenow;
		if ( 'plugins.php' == $pagenow ) {
			$class = esc_attr( 'notice notice-error is-dismissible' );
			$message = esc_html__('woocommerce_ordf For WooCommerce Plugin needs WooCommerce to be installed and active.', WORDF_LANG);
			printf('<div class="%1$s"><p>%2$s</p></div>', esc_html($class), esc_html($message));
		}
	}
/*-------------------------------------  install ----------------------------------------->*/
	public function install()
	{
		global $wpdb;
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		$sql = "CREATE TABLE IF NOT EXISTS " . WORDF_UPLOADS_TABLE . " (
				id int(11) NOT NULL AUTO_INCREMENT,
				category int(11),
				type int(11),
				user_id int(11),
				order_id int(11),
				folder_name VARCHAR(100),
				attributes LONGTEXT,
				label VARCHAR(255),
				remote int(1),
				vip int(1),
				confirmed int(1),
				expires datetime,
				expire_enabled int(1),
				updated datetime,
				created datetime,
				PRIMARY KEY (id)
			) CHARACTER SET utf8 COLLATE utf8_general_ci";
			//logs table 
		$sql_logs = "CREATE TABLE IF NOT EXISTS " . WORDF_LOGS_TABLE . " (
				id int(11) NOT NULL AUTO_INCREMENT,
				record_id int(11),
				user_id int(11),
				type VARCHAR(255),
				value int(1),
				expires VARCHAR(100),
				comment LONGTEXT,
				created datetime,
				PRIMARY KEY (id)
			) CHARACTER SET utf8 COLLATE utf8_general_ci";
			
			dbDelta($sql);
			dbDelta($sql_logs);
			//categories
		$sql_categories = "CREATE TABLE IF NOT EXISTS " . WORDF_CATEGORIES_TABLE . " (
					id int(11) NOT NULL AUTO_INCREMENT,
					name VARCHAR(255),
					PRIMARY KEY (id)
				) CHARACTER SET utf8 COLLATE utf8_general_ci";

			dbDelta($sql_categories);
			$db_actions=new woordf_categories_module();
			$db_actions->insert_default_category();
			update_option('WORDF_VERSION', WORDF_VERSION);
			update_option('woordf_options','');
			update_option('woordf_default_parameters','');
			
			if(!is_dir(WORDF_UPLOADS_FOLDER)){
				mkdir(WORDF_UPLOADS_FOLDER);
			}

	}
	
	//menu items 
	
	public function add_menu_items()
	{
		add_menu_page(WORDF_ADMIN_PAGE_SLUG,__("Woo order files",WORDF_LANG),'edit_posts',"woordf_records",array(&$this, 'woordf_records'),'dashicons-paperclip',57);//comp was: 'edit_posts'
		add_submenu_page(WORDF_ADMIN_PAGE_SLUG, __('Order files',WORDF_LANG), __('Order files',WORDF_LANG), 'edit_posts',WORDF_ADMIN_PAGE_SLUG, array(&$this,'woordf_records'));
		add_submenu_page(WORDF_ADMIN_PAGE_SLUG, __('Upload new files',WORDF_LANG), __('Upload new files',WORDF_LANG), 'edit_posts',WORDF_ADMIN_NEW_PAGE_SLUG, array(&$this,'woordf_new'));
		add_submenu_page(WORDF_ADMIN_PAGE_SLUG, __('Categories',WORDF_LANG), __('Categories',WORDF_LANG), 'edit_posts',WORDF_ADMIN_CATEGORIES_PAGE_SLUG, array(&$this,'woordf_categories'));
		add_submenu_page(WORDF_ADMIN_PAGE_SLUG, __('Options page',WORDF_LANG), __('Options page',WORDF_LANG), 'edit_posts',WORDF_OPTIONS_PAGE_SLUG, array(&$this,'woordf_options'));
		add_submenu_page(WORDF_ADMIN_PAGE_SLUG, __('Logs',WORDF_LANG), __('Logs',WORDF_LANG), 'edit_posts',WORDF_ADMIN_LOGS_PAGE_SLUG, array(&$this,'woordf_logs'));

	}


	//new  
	
	public function woordf_new(){
		include_once WORDF_DIR.'/views/admin/new.php';
	}
	//categories page 
	
	public function woordf_categories(){
		include_once WORDF_DIR.'/inc/admin_tables/woordf_categories_table.php';
		$table = new woordf_categories_table();
	    $table->prepare_items();
		include_once WORDF_DIR.'/views/admin/categories.php';
	}
	//options page 
	
	public function woordf_options(){
		include_once WORDF_DIR.'/modules/admin_options_module.php';
		include_once WORDF_DIR.'/views/admin/options.php';
	}
	
	//logs page 
	
	public function woordf_logs(){
		woordf_remove_expired_data_manual();
		include_once WORDF_DIR.'/inc/admin_tables/woordf_logs_table.php';
		$table = new woordf_logs_table();
	    $table->prepare_items();
		include_once WORDF_DIR.'/views/admin/logs.php';
	}
	//admin page 
	
	public function woordf_records(){

		if(!isset($_GET['record'])):
			//view
			include_once WORDF_DIR.'/inc/admin_tables/woordf_table.php';
			$table = new woordf_table();
	        $table->prepare_items();
			$filters=woordf_admin_order_filters();
			
			include_once WORDF_DIR.'/views/admin/records.php';
			
        else:
        	//edit record
        	$db_actions=new woordf_records_module();
	    	$record_id=(isset($_GET['record']))?(int)$_GET['record']:0;
			if($record_id!=0) $results=$db_actions->get_where(array('id'=>$record_id));		

			if(isset($results) && !empty($results)){
				$record=$results[0];
				$db_logs_actions=new woordf_logs_module();
				$record_logs=$db_logs_actions->get_logs($record['id']);
			}

        	include_once WORDF_DIR.'/views/admin/single_record.php';
        	
        endif;
	}

	//-------------------------------------  uninstall ----------------------------------------->
	public function uninstall() {
		global $wpdb;
		$wpdb->query("DROP TABLE IF EXISTS " . WORDF_UPLOADS_TABLE);
		$wpdb->query("DROP TABLE IF EXISTS " . WORDF_LOGS_TABLE);
		$wpdb->query("DROP TABLE IF EXISTS " . WORDF_CATEGORIES_TABLE);
		
	}
	//language set
	public function set_language()
	{
		load_plugin_textdomain(WORDF_LANG, false, basename(dirname( __FILE__ )) . '/languages/');
	}
	
	//-- front-end scripts
	
	public function frontend_scripts()
	{
		global $post;
		$version=woordf_get_version();
		$template_name='';
		$endpoint_slug=WC()->query->get_current_endpoint();
		if(isset($post) && isset($post->ID)){
			$template_name = get_post_meta( $post->ID, '_wp_page_template', true );
		}
		

		if ( $template_name == 'embed.php' || $endpoint_slug=='order-downloads') {
			//wp_register_style( WORDF_PREFIX.'bootstrap','https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css',array(),'5.2.3');
			//wp_enqueue_style(WORDF_PREFIX.'bootstrap');
			//bootstrap css 
			wp_register_style( 'bootstrap_css', WORDF_URL.'assets/css/bootstrap-part.css',array(),'1.1.1');
			wp_enqueue_style('bootstrap_css');
		}
		
		if(is_account_page() && $endpoint_slug=='order-downloads'){
			wp_register_script(WORDF_PREFIX.'_bootstrap_js', WORDF_URL.'libs/bootstrap/js/bootstrap.min.js',array('jquery'),'3.4.1',false);
			wp_enqueue_script(WORDF_PREFIX.'_bootstrap_js');

			
			wp_register_script(WORDF_PREFIX.'_my_acc_js', WORDF_URL . 'assets/js/my_account.js',array('jquery',WORDF_PREFIX.'_bootstrap_js'),$version,false);
			wp_enqueue_script(WORDF_PREFIX.'_my_acc_js');
			
			wp_register_script('lightb_js', WORDF_URL.'libs/photoswipe/photoswipe.js', array('jquery'));
			wp_enqueue_script('lightb_js');
			wp_register_script('lightb_ui_js', WORDF_URL.'libs/photoswipe/photoswipe-ui-default.min.js', array('jquery'));
			wp_enqueue_script('lightb_ui_js');

			
			wp_enqueue_style('lightbox_css',  WORDF_URL.'libs/photoswipe/photoswipe.css');
			wp_register_style( 'lightbox_skin_css', WORDF_URL.'libs/photoswipe/default-skin/default-skin.css',array(),'1.1.1');
			wp_enqueue_style('lightbox_skin_css');

			wp_localize_script(WORDF_PREFIX.'_my_acc_js', 'woordf_vars', array(
				'ajax_url'=> admin_url('admin-ajax.php'),
				'downloads_url'=>woordf_order_downloads_url(),
				'oid'=>(isset($_GET['oid']))?$_GET['oid']:0,
				'rid'=>(isset($_GET['rid']))?$_GET['rid']:0
			));
			//add modal
			add_action('wp_head', array($this, 'my_acc_modals'));


		 	wp_dequeue_script( 'elementor-dialog' );
		    wp_deregister_script( 'elementor-dialog' );
			wp_dequeue_script( 'elementor-frontend' );
		    wp_deregister_script( 'elementor-frontend' );
		    
			add_action( 'elementor/frontend/after_register_scripts',function(){
				wp_deregister_script( 'elementor-frontend-modules-js' );
				wp_dequeue_script( 'elementor-frontend-modules-js' );
				wp_deregister_script( 'bdt-uikit-js' );
				wp_dequeue_script( 'bdt-uikit-js' );
		 		wp_dequeue_script( 'elementor-dialog' );
		   		wp_deregister_script( 'elementor-dialog' );
			} );
		}
		if(is_account_page() ||  $template_name == 'embed.php'){
			wp_register_style( WORDF_PREFIX.'_my_account_css', WORDF_URL.'assets/css/my_account.css',array('bootstrap_css'),$version);
			wp_enqueue_style(WORDF_PREFIX.'_my_account_css');
		}

	}
	
	//-- back-end scripts

	public function backend_scripts($hook)
	{
		//echo 'hook: '.$hook;
		$screen = get_current_screen();
		$version=woordf_get_version();
		global $post_type;


		if($post_type=='product'){
			if (!wp_script_is( 'jquery' ) ) {
	        	wp_enqueue_script('jquery');
	   		}
			wp_register_style( WORDF_PREFIX.'_admin_style', WORDF_URL.'assets/css/admin_style.css',array(),$version);
			wp_enqueue_style(WORDF_PREFIX.'_admin_style');
		}
		if($hook==WORDF_WP_ADMIN_PAGE_SLUG.'_page_'.WORDF_ADMIN_NEW_PAGE_SLUG || ($hook=='toplevel_page_'.WORDF_ADMIN_PAGE_SLUG && isset($_GET['record']))){
			if (!wp_script_is( 'jquery' ) ) {
	        	wp_enqueue_script('jquery');
	   		}
			wp_register_script(WORDF_PREFIX.'_jquery_uploader_js', WORDF_URL . 'libs/jquery_uploader/jquery.uploadfile.min.js',array('jquery'),'4.0.11',false);
			wp_enqueue_script(WORDF_PREFIX.'_jquery_uploader_js');
			wp_register_style( WORDF_PREFIX.'_jquery_uploader_css', WORDF_URL.'libs/jquery_uploader/jquery-file-upload.css',array(),'4.0.11');
			wp_enqueue_style(WORDF_PREFIX.'_jquery_uploader_css');
			
			
			wp_enqueue_script( 'jquery-ui-datepicker' );
			wp_enqueue_style('jquery-ui-datepicker_css', 'https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css');
			
			wp_register_script('lightb_js', WORDF_URL.'libs/photoswipe/photoswipe.js', array('jquery'));
			wp_enqueue_script('lightb_js');
			wp_register_script('lightb_ui_js', WORDF_URL.'libs/photoswipe/photoswipe-ui-default.min.js', array('jquery'));
			wp_enqueue_script('lightb_ui_js');

			wp_enqueue_style('lightbox_css',  WORDF_URL.'libs/photoswipe/photoswipe.css');
			wp_register_style( 'lightbox_skin_css', WORDF_URL.'libs/photoswipe/default-skin/default-skin.css',array(),'1.1.1');
			wp_enqueue_style('lightbox_skin_css');
			
			//bootstrap 

			//js
			wp_register_script(WORDF_PREFIX.'_bootstrap_js', WORDF_URL.'libs/bootstrap/js/bootstrap.min.js',array('jquery'),'3.4.1',false);
			wp_enqueue_script(WORDF_PREFIX.'_bootstrap_js');
			

		}
		if($hook=='toplevel_page_'.WORDF_ADMIN_PAGE_SLUG || $hook==WORDF_WP_ADMIN_PAGE_SLUG.'_page_'.WORDF_OPTIONS_PAGE_SLUG || $hook==WORDF_WP_ADMIN_PAGE_SLUG.'_page_'.WORDF_ADMIN_NEW_PAGE_SLUG || ($hook=='toplevel_page_'.WORDF_ADMIN_PAGE_SLUG && isset($_GET['record'])) || $hook==WORDF_WP_ADMIN_PAGE_SLUG.'_page_'.WORDF_ADMIN_LOGS_PAGE_SLUG || $hook==WORDF_WP_ADMIN_PAGE_SLUG.'_page_'.WORDF_ADMIN_CATEGORIES_PAGE_SLUG ){
			//bootstrap css 
			wp_register_style( 'bootstrap_css', WORDF_URL.'assets/css/bootstrap-part.css',array(),'1.1.1');
			wp_enqueue_style('bootstrap_css');
			if($hook==WORDF_WP_ADMIN_PAGE_SLUG.'_page_'.WORDF_OPTIONS_PAGE_SLUG){

	        	wp_enqueue_script( 'jquery-ui-core');
				wp_enqueue_style( 'jquery-ui-theme-smoothness','//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css');
				
				wp_register_script(WORDF_PREFIX.'_jquery_ui_js','https://code.jquery.com/ui/1.10.4/jquery-ui.js',array(),'1.10.4',true);
				wp_enqueue_script( WORDF_PREFIX.'_jquery_ui_js');
			}
			if($hook==WORDF_WP_ADMIN_PAGE_SLUG.'_page_'.WORDF_ADMIN_LOGS_PAGE_SLUG){
				wp_enqueue_script( 'jquery-ui-datepicker' );
				wp_enqueue_style('jquery-ui-datepicker_css', 'https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css');
			}
			
			
			
			wp_register_style( WORDF_PREFIX.'_admin_style', WORDF_URL.'assets/css/admin_style.css',array(),$version);
			wp_enqueue_style(WORDF_PREFIX.'_admin_style');
			

		}




	
	}
}
//-----------------------------------------initiation ----------------------------
if(class_exists('woocommerce_woordf_class')){
		new woocommerce_woordf_class();
		register_uninstall_hook(__FILE__, array('woocommerce_woordf_class', 'uninstall' ));
}

