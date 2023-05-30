<?php
//-------------========================================    table class for player statistics "bigcinema_List_Table_statistics" =========================---------------------------------
if( ! class_exists( 'WP_List_Table' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}
class woordf_table extends WP_List_Table
{
	public function __construct() {

        parent::__construct(
            array(
                'singular' => 'singular_form',
                'plural'   => 'plural_form',
                'ajax'     => false
            )
        );

    }
 // bulk actions
   public function get_bulk_actions() {
	   	
		$actions = array(
			  'delete'    => __('Remove',WORDF_LANG),

		);
		
	  	return $actions;
	}
		//actions to collumn
	function column_actions($item) {
	  	$actions = array(
	            'view'    => sprintf('<a href="?page=%s&record=%s">%s</a>',$_REQUEST['page'],$item['id'],__('Edit',WORDF_LANG)),
	            'delete'    => sprintf('<a href="?page=%s&id=%s&action=delete">%s</a>',$_REQUEST['page'],$item['id'],__('Remove',WORDF_LANG)),
	     );
	     

	  return sprintf('%1$s', $this->row_actions($actions) );
	}
	private function nonce_field($content,$error)
	{
		if($error) $cl='notice notice-error is-dismissible';
		else $cl='notice notice-success is-dismissible';
		return '<div style="min-height:30px;line-height:25px;font-size:24px;margin-left:0;margin-top:10px;padding:7px 5px 3px 5px" class="'.$cl.'">'.$content.'</div>';
	}
	public function process_row_action() {
		  global $wpdb;
		  $action = $this->current_action();
		  if (isset($_GET['id']))
		  {
			 switch ( $action ) {
	            case 'delete':
	            	$db_actions=new woordf_records_module();
	            	$db_actions->remove($_GET['id']);
	                echo $this->nonce_field(__('Record was removed!',WORDF_LANG),false);
	                break;

	            default:
	                // do nothing or something else
	                return;
	                break;
	        }
		  }

	 }
	public function process_bulk_action() {
		global $wpdb;
        // security check!
        if ( isset( $_POST['_wpnonce'] ) && ! empty( $_POST['_wpnonce'] ) ) {

            $nonce  = filter_input( INPUT_POST, '_wpnonce', FILTER_SANITIZE_STRING );
            $action = 'bulk-' . $this->_args['plural'];

            if ( ! wp_verify_nonce( $nonce, $action ) )
                wp_die( 'Nope! Security check failed!' );

        }

        $action = $this->current_action();
		
	        switch ( $action ) {

	            case 'delete':
	                if (isset($_POST['id']))
	                {
	                	$db_actions=new woordf_records_module();
						foreach($_POST['id'] as $id){
							
							$db_actions->remove($id);
						}
						echo $this->nonce_field(count($_POST['id']).' '.__('Record(-s) was removed!',WORDF_LANG),false);
					}
	                break;    
     
	            default:
	                // do nothing or something else
	                return;
	                break;
	        }


        return;
    }

	//adding checkbox for bulk actions
	public function column_id($item) {
		
        return sprintf(
            '<input type="checkbox" name="id[]" value="%s" /><i>%s</i>', $item['id'],$item['id']
        );
        
        return $item['id'];    
    }
    public function prepare_items()
    {
    	$this->process_bulk_action();
    	$this->process_row_action();
        $columns = $this->get_columns();
        $hidden = $this->get_hidden_columns();
        $sortable = $this->get_sortable_columns();
        $data = $this->table_data();
        if(!empty($_GET['orderby'])) usort( $data, array( &$this, 'sort_data' ) );

        $per_page_default = 20;
        $perPage=get_option(WORDF_PX.'records_per_page');
        $perPage=($perPage!='')?$perPage:$per_page_default;
        $currentPage = $this->get_pagenum();
        $totalItems = count($data);

        $this->set_pagination_args( array(
            'total_items' => $totalItems,
            'per_page'    => $perPage
        ) );

        $data = array_slice($data,(($currentPage-1)*$perPage),$perPage);

        $this->_column_headers = array($columns, $hidden, $sortable);
        $this->items = $data;
        
    }


    /**
     * Override the parent columns method. Defines the columns to use in your listing table
     *
     * @return Array
     */
    public function get_columns()
    {
        $columns = woordf_admin_record_tabel_labels();
        unset($columns['file_url']);
        unset($columns['folder_name']);

  			
        return $columns;
    }

    /**
     * Define which columns are hidden
     *
     * @return Array
     */
    public function get_hidden_columns()
    {
        return array();
    }

    /**
     * Define the sortable columns
     *
     * @return Array
     */
    public function get_sortable_columns()
    {
         $sortable_columns = array(
         	'created' => array('created',false),
         	'order' => array('order',false),
         	'user' => array('user',false),
  		);

  		return $sortable_columns;
    }

    /**
     * Get the table data
     *
     * @return Array
     */
    private function table_data()
    {
    	global $wpdb;

		$data=array();
		$db_actions=new woordf_records_module();
		$db_categories_actions=new woordf_categories_module();
		$results=array();
		$show_all=true;
		if(isset($_GET['woordf_filters'])){
			$fields=woordf_admin_order_filters();
			$filters=array();
			foreach($fields as $field){
				$field_name=$field['name'];
				if(isset($_GET[$field_name]) && $_GET[$field_name]!='' && $_GET[$field_name]!='0') $filters[$field_name]=$_GET[$field_name];
			}
			if(!empty($filters)){
				if(isset($_GET['slabel']) && $_GET['slabel']!=''){
					$results=$db_actions->get_where($filters,"AND",array(),array('created','DESC'),array('label'=>$_GET['slabel']));
				}
				else $results=$db_actions->get_where($filters);
				 $show_all=false;
			}
			else if(isset($_GET['slabel']) && $_GET['slabel']!=''){
				$results=$db_actions->get_where_search(array('label'=>$_GET['slabel']));
			}
		}
		if(empty($results) && $show_all) $results=$db_actions->get_table_data();
		
		if(!empty($results)) {

			foreach($results as $result)
			{	
				$name='-';
				if($result['user_id']!=0){
					$user_info = get_userdata($result['user_id']);
					$name=($user_info)?$user_info->user_login:'#'.$result['user_id'];
				}
				

		        $data[] = array(
		                    'id'        => $result['id'],
		                    'order'	=>($result['order_id']==0)?'-':$result['order_id'],
		    				'label'=>$result['label'],
		    				'user'      => $name,
		    				'type'  	=> woordf_get_type_label($result['type']),
		    				'category'=>$db_categories_actions->get_field_where(array('id'=>$result['category']),'name'),
		    				'remote'=>woordf_get_icon_by_val('remote',$result['remote']),
		    				'vip'=>woordf_get_icon_by_val('vip',$result['vip']),
		    				'confirmed'=>($result['vip'])?woordf_get_icon_by_val('confirmed',$result['confirmed']):'-',
		    				'expires'=>($result['expire_enabled'])?woordf_get_datetime($result['expires'],'Y-m-d'):__('Not expires', WORDF_LANG),
		    				'updated'   => woordf_get_datetime($result['updated'],'Y-m-d H:i:s'),
		    				'created'   => woordf_get_datetime($result['created'],'Y-m-d H:i:s'),
		    				'actions'	=>'<a href="'.admin_url( "admin.php?page=".WORDF_ADMIN_PAGE_SLUG).'&order='.$result['id'].'">'.__('View', WORDF_LANG).'</a>',
		                    );

				
        
		    }
		}

        return $data;
    }


    /**
     * Define what data to show on each column of the table
     *
     * @param  Array $item        Data
     * @param  String $column_name - Current column name
     *
     * @return Mixed
     */
    public function column_default( $item, $column_name )
    {

       return $item[ $column_name ];

    }

    /**
     * Allows you to sort the data by the variables set in the $_GET
     *
     * @return Mixed
     */
    private function sort_data( $a, $b )
    {
        // Set defaults
        $orderby = 'id';
        $order = 'DESC';

        // If orderby is set, use this as the sort column
        if(!empty($_GET['orderby']))
        {
            $orderby = $_GET['orderby'];
        }

        // If order is set use this as the order
        if(!empty($_GET['order']))
        {
            $order = $_GET['order'];
        }


        $result = strcmp( $a[$orderby], $b[$orderby] );

        if($order === 'asc')
        {
            return $result;
        }

        return -$result;
    }
}
?>