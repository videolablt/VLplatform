<?php
class woordf_categories_module extends woordf_db_module
{
    private static $instance;
    
    public function __construct() { 
        parent::__construct();
        
        $this->setTableName(WORDF_CATEGORIES_TABLE);
        $this->setFields( array(
        	'id'=>array('type'=>'int','default'=>''),
        	'name'=>array('type'=>'string','default'=>''),

        ) );

    }
    
    //insert_default_category
    
    public function insert_default_category(){
		$data=$this->get_table_data(array('id'=>'ASC'));
		if(empty($data)){
			$this->insert_row(array('name'=>__("Uncategorized",WORDF_LANG)));
		}
	}
	
	public function category_not_exist($name){
		$data=$this->get_where_binary('name='.$this->add_quotes($name));
		return (empty($data));
	}
	public function get_where_binary($where){
		global $wpdb;
		$where_sql=' WHERE BINARY '.$where;

		$sql="SELECT * FROM " . $this->tablename.$where_sql;
		$records = $wpdb->get_results($sql,ARRAY_A);	
		return $records;
	}

}