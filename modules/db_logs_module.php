<?php
class woordf_logs_module extends woordf_db_module
{
    private static $instance;
    
    public function __construct() { 
        parent::__construct();
        
        $this->setTableName(WORDF_LOGS_TABLE);
        $this->setFields( array(
        	'id'=>array('type'=>'int','default'=>''),
        	'user_id'=>array('type'=>'int','default'=>0),
        	'type'=>array('type'=>'string','default'=>''), 
        	'record_id'=>array('type'=>'int'),
        	'value'=>array('type'=>'int','default'=>0),
        	'comment'=>array('type'=>'string','default'=>''),
        	'created'=>array('type'=>'datetime','default'=>woordf_get_datetime()),
        ) );

    }
    
    //table users

	public function get_logs($record_id){
		global $wpdb;
		$sql='SELECT *  FROM ' . $this->tablename.' WHERE record_id='.$record_id.' AND (type = "rate" OR type = "admin") ORDER BY created ASC';
		$records = $wpdb->get_results($sql,ARRAY_A);	
		return $records;
	}
	
	//admin logs
	public function get_admin_logs($filters=array(),$search=array()){
		global $wpdb;
		
		$where_sql=' WHERE ';
		$where_arr=array();
		$operator="AND";
		if(!empty($filters)){
			if(isset($filters['date_from']) && $filters['date_from']!='') $where_arr[]='created >= "'.$filters['date_from'].'"';
			if(isset($filters['date_to']) && $filters['date_to']!='') $where_arr[]='created <= "'.$filters['date_to'].'"';
		}

		if(!empty($search)){
			foreach($search as $key=>$val){
				$where_arr[]='('.$key.' LIKE "'.$val.'%" OR '.$key.' LIKE "%'.$val.'%" OR '.$key.' LIKE "%'.$val.'")';
			}
		}
		if(count($where_arr)>0) $where_sql.=implode(" ".$operator." ",$where_arr);
		
		
		
		if(count($where_arr)>0) $sql="SELECT * FROM " . $this->tablename.$where_sql." ORDER BY created DESC";
		else $sql="SELECT *  FROM " . $this->tablename." WHERE 1 ORDER BY created DESC";
		//file_put_contents(dirname(__FILE__).'/test3.txt', $sql);
		$records = $wpdb->get_results($sql,ARRAY_A);	
		return $records;
	}
	
	//admin_made_changes
	
	public function admin_made_changes($record_id){
		global $wpdb;
		$sql="SELECT *  FROM " . $this->tablename." WHERE record_id=".$record_id.' ORDER BY created DESC';
		$records = $wpdb->get_results($sql,ARRAY_A);	

		$admin_made_changes=(empty($records) || $records[0]['type']=='admin');
		return $admin_made_changes;
	}
	//user rating count
	public function get_user_rating_count($record_id){
		global $wpdb;
		$sql="SELECT *  FROM " . $this->tablename." WHERE record_id=".$record_id.' AND type="rate" ORDER BY created ASC';
		$records = $wpdb->get_results($sql,ARRAY_A);
		if(!$records) return 0;	
		else return count($records);
	}
	
	//get_latest_log
	
	public function get_latest_log($record_id){
		global $wpdb;
		$sql="SELECT *  FROM " . $this->tablename." WHERE record_id=".$record_id.' ORDER BY created DESC LIMIT 1';
		$records = $wpdb->get_results($sql,ARRAY_A);
		if(!$records) return array();	
		else return $records[0];
	}
	public function if_field_exist($field_key){
		$exist=false;
		foreach($this->fields as $key=>$value){
			if($field_key==$key) $exist=true;
		}
		return $exist;
	}
	//not_exist
	public function not_exist($data){
		global $wpdb;
		$where_arr=array();
		unset($data['comment']);

		foreach($data as $key=>$value){
			if($this->if_field_exist($key)){
				if($key=='type' || $key=='expires') $val="'".$value."'";
				else $val=$value;
				$where_arr[]=$key.' = '.$val;
			}
		}
		$where_sql='1';
		if(count($where_arr)>0) $where_sql=implode(" AND ",$where_arr);
		$sql="SELECT *  FROM " . $this->tablename." WHERE ".$where_sql." ORDER BY created ASC";
		$records = $wpdb->get_results($sql,ARRAY_A);
		//file_put_contents(dirname(__FILE__).'/testassql.txt', "SELECT *  FROM " . $this->tablename." WHERE ".$where_sql." ORDER BY created ASC - count:".count($records));
		return (count($records)==0);
	}


}