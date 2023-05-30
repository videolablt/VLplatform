<?php
//-------------========================================    table class for player statistics "bigcinema_List_Table_statistics" =========================---------------------------------
if( ! class_exists( 'WP_List_Table' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}
class woordf_categories_table extends WP_List_Table
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
	            'edit'    => sprintf('<a href="#" class="edit_category">%s</a>',__('Edit',WORDF_LANG)),
	            'delete'    => ($item['id']!=1)?sprintf('<a href="#" class="remove_category">%s</a>',__('Remove',WORDF_LANG)):__('(Deffault)',WORDF_LANG),
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
	            	if($_GET['id']!=1){
		            	$db_actions=new woordf_categories_module();
		            	$db_actions->remove_record($_GET['id']);
		                echo $this->nonce_field(__('Record was removed!',WORDF_LANG),false);
					}
					else echo $this->nonce_field(__('Default category can not be removed!',WORDF_LANG),false);

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
	                	$db_actions=new woordf_categories_module();
	                	$default_selected=false;
						foreach($_POST['id'] as $id){
			            	if($id!=1){
				            	$db_actions->remove_record($id);
							}
							else{
								$default_selected=true;
							}
							
						}
						$count=($default_selected)?count($_POST['id'])-1 : count($_POST['id']);
						echo $this->nonce_field($count.' '.__('Record(-s) was removed!',WORDF_LANG),false);
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
		if($item['id']!=1){
	        return sprintf(
	            '<input type="checkbox" name="id[]" value="%s" /><i>%s</i>', $item['id'],$item['id']
	        );
		}

        return sprintf(
	            '<input type="checkbox" name="id[]" value="%s" disabled/><i>%s</i>', $item['id'],$item['id']
	        );;    
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
        $perPage=get_option(WORDF_PX.'categories_per_page');
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
        $columns = woordAdmin_categories_columns();

  			
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
         	'id' => array('created',false),
         	'name' => array('label',false),
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
		$db_actions=new woordf_categories_module();

		$results=$db_actions->get_table_data(array('id'=>'ASC'));
		
		if(!empty($results)) {

			foreach($results as $result)
			{	


		        $data[] = array(
                    'id'  => $result['id'],
                    'name' =>'<div class="inner"><span class="name">'.$result['name'].'</span><div class="input"><input type="text" value="'.$result['name'].'" autocomplete="off" /><div class="btn update" data-id="'.$result['id'].'" title="'.__('Update',WORDF_LANG).'">'.woordf_output_icon('save','',false).'</div></div></div>',
    				'actions'	=>'',
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