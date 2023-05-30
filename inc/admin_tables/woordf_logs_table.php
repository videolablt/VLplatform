<?php
//-------------========================================    table class for player statistics "bigcinema_List_Table_statistics" =========================---------------------------------
if( ! class_exists( 'WP_List_Table' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}
class woordf_logs_table extends WP_List_Table
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
	  	$actions = array();
	     

	  return sprintf('%1$s', $this->row_actions($actions) );
	}
	private function nonce_field($content,$error)
	{
		if($error) $cl='notice notice-error is-dismissible';
		else $cl='notice notice-success is-dismissible';
		return '<div style="min-height:30px;line-height:25px;font-size:24px;margin-left:0;margin-top:10px;padding:7px 5px 3px 5px" class="'.$cl.'">'.$content.'</div>';
	}



	//adding checkbox for bulk actions
	public function column_id($item) {

        return '#'.$item['id'];    
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

        $perPage = 20;
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
        $columns = woordf_admin_logs_columns();
  			
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
		$db_actions=new woordf_logs_module();
		$results=array();
		$filters=array();
		$search=array();
		if(isset($_GET['woordf_filters'])){
			if(isset($_GET['keyword']) && $_GET['keyword']!='') $search=array('comment'=>$_GET['keyword']);
			if(isset($_GET['date_from']) && $_GET['date_from']!='') $filters['date_from']=$_GET['date_from'];
			if(isset($_GET['date_to']) && $_GET['date_to']!='') $filters['date_to']=$_GET['date_to'];
		}
		$results=$db_actions->get_admin_logs($filters,$search);
		
		if(!empty($results)) {

			foreach($results as $result)
			{	
				$name='-';
				if($result['user_id']!=0){
					$user_info = get_userdata($result['user_id']);
					$name=($user_info)?$user_info->user_login:'#'.$result['user_id'];
				}

		        $data[] = array(
		                    'id'        => $result['record_id'],
		                    'user_id'		=>$name,
		                    'created'	=>woordf_get_datetime($result['created'],'Y-m-d H:i'),
		                    'type'        => woordf_get_label_by_log_field('type',$result),
		                    'comment'	=>$result['comment'],

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