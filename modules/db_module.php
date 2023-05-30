<?php
class woordf_db_module
{
	var $tablename;
	var $fields;
    public function __construct() { 

    }
    
    //set table name 
    
	public function setTableName($name){
		$this->tablename=$name;
	}
	
    //set table columns 
    
	public function setFields($fields){
		$this->fields=$fields;
	}
	
	public function field_exist($field_key){
		$exist=false;
		foreach($this->fields as $key=>$value){
			if($field_key==$key) $exist=true;
		}
		return $exist;
	}
    //get columns fields 
    
	public function getFields(){
		return $this->fields;
	}
	public function get_columns_types(){
		$result=array();
		foreach($this->fields as $key=>$value){
			$result[]=($value['type']=='int' || $value['type']=='float')?'%d':'%s';
		}
		return $result;
	}
	//prepare data for db 
	public function prepare_data($columns_data){
		$result=array();

		if($this->field_exist('label')){
			if((!isset($columns_data['label']) || $columns_data['label']=='') && isset($columns_data['filename'])){
				$columns_data['label']=woordf_get_file_name($columns_data);
			}
			if(isset($columns_data['order_id']) && !isset($columns_data['user_id'])){
				if($columns_data['order_id']==0) $columns_data['user_id']=0;
				else{
					$order = wc_get_order($columns_data['order_id']);
					$columns_data['user_id']=$order->get_user_id();
				}
			}
		}


		
		foreach($this->fields as $key=>$value){
			if(isset($value['default']) && !isset($columns_data[$key])){
				$result[$key]=$value['default'];
			}
			else{
				$result[$key]=$this->format_value($value['type'],$columns_data,$key);
			}
			
		}


		return $result;
	}
	
	public function prepare_data_for_update($columns_data){
		$result=array();
		foreach($this->fields as $key=>$value){
			if(isset($columns_data[$key])) $result[$key]=$this->format_value($value['type'],$columns_data,$key);
			
		}
		if($this->field_exist('order_id')){
			if(isset($columns_data['order_id'])){
				if($columns_data['order_id']==0) $result['user_id']=0;
				else{
					$order = wc_get_order($columns_data['order_id']);
					$result['user_id']=$order->get_user_id();
				}
			}
		}

		if($this->field_exist('updated')){
			$result['updated']=woordf_get_datetime();
		}

		return $result;
	}
	//format value 
	
	public function format_value($type,$columns_data,$key){
		$result='';
		switch ($type) {
		    case 'int':
		        $result=(isset($columns_data[$key]) && $columns_data[$key]!='')?(int)$columns_data[$key]:0;
		        break;
		    case 'float':
		        $result=(isset($columns_data[$key]) && $columns_data[$key]!='')? floatval($columns_data[$key]):0;
		        break;
		    case 'array':
		        $result=(isset($columns_data[$key]) && $columns_data[$key]!='')?maybe_serialize($columns_data[$key]):maybe_serialize(array());
		        break;
		    case 'datetime':
		        $result=(isset($columns_data[$key]) && $columns_data[$key]!='')?woordf_get_datetime($columns_data[$key],'Y-m-d').' 00:00:00':'';
		        break; 
		    case 'string':
			default:
				$result=(isset($columns_data[$key]) && $columns_data[$key]!='')? $columns_data[$key]:'';
		}
		return $result;
	}
	//insert record 
	
	public function insert_row($columns_data){
		global $wpdb;
		$columns=$this->prepare_data($columns_data);
		
		$inserted=$wpdb->insert($this->tablename ,$columns, $this->get_columns_types() );

		return $wpdb->insert_id;
	}
	
	//get where
	
	public function get_where($where,$operator="AND",$operators=array(),$orderby=array('created','DESC'),$search=array()){
		global $wpdb;
		$where_sql=' WHERE ';
		$orderby_sql=" ORDER BY ".$orderby[0]." ".$orderby[1];
		$where_arr=array();
		$ind=0;
		foreach($where as $key=>$value){
			if(is_array($value)) $where_arr[]=$key.' IN ('.implode(",",$value).')';
			else if(!empty($operators)) $where_arr[]=$key.' '.$operators[$ind].' '.$value;
			else $where_arr[]=$key.' = '.$value;
			$ind++;
		}
		if(!empty($search)){
			foreach($search as $key=>$val){
				$where_arr[]='('.$key.' LIKE "'.$val.'%" OR '.$key.' LIKE "%'.$val.'%" OR '.$key.' LIKE "%'.$val.'")';
			}
		}
		if(count($where_arr)>0) $where_sql.=implode(" ".$operator." ",$where_arr);
		else $where_sql.=$where_arr[0];
		$sql="SELECT * FROM " . $this->tablename.$where_sql.$orderby_sql;
		$records = $wpdb->get_results($sql,ARRAY_A);	
		return $records;
	}

	//returns field
	
	public function get_field_where($where,$field){
		global $wpdb;
		$where_sql=' WHERE ';
		$operator="AND";
		$where_arr=array();
		$ind=0;
		foreach($where as $key=>$value){
			if(is_array($value)) $where_arr[]=$key.' IN ('.implode(",",$value).')';
			else $where_arr[]=$key.' = '.$value;
			$ind++;
		}
		if(count($where_arr)>1) $where_sql.=implode(" ".$operator." ",$where_arr);
		else $where_sql.=$where_arr[0];
		$sql="SELECT * FROM " . $this->tablename.$where_sql." LIMIT 1";
		$records = $wpdb->get_results($sql,ARRAY_A);	
		return (!empty($records) && isset($records[0][$field]))?$records[0][$field]:'';
	}
	//search only 
	
	public function get_where_search($search=array()){
		global $wpdb;
		$operator="AND";
		$orderby=array('created','DESC');
		$where_sql=' WHERE ';
		$orderby_sql=" ORDER BY ".$orderby[0]." ".$orderby[1];
		$where_arr=array();

		if(!empty($search)){
			foreach($search as $key=>$val){
				$where_arr[]='('.$key.' LIKE "'.$val.'%" OR '.$key.' LIKE "%'.$val.'%" OR '.$key.' LIKE "%'.$val.'")';
			}
		}
		if(count($where_arr)>0) $where_sql.=implode(" ".$operator." ",$where_arr);
		else $where_sql.=$where_arr[0];
		$sql="SELECT * FROM " . $this->tablename.$where_sql.$orderby_sql;
		$records = $wpdb->get_results($sql,ARRAY_A);	
		return $records;
	}

	

	//get table data
	
	public function get_table_data($orderby=array('created'=>'DESC'),$limit=0){
		global $wpdb;
		$orderby_sql='';
		if(!empty($orderby)){
			$orderby_sql=' ORDER BY ';
			foreach($orderby as $key=>$value) $orderby_sql.=$key.' '.$value;
		}
		$limit_sql='';
		if($limit!=0){
			$limit_sql.=" LIMIT ".$limit;
		}
		$sql="SELECT * FROM " . $this->tablename.' WHERE 1'.$orderby_sql.$limit_sql;
		$records = $wpdb->get_results($sql,ARRAY_A);	
		return $records;
	}
	//update
	public function update($id,$columns_data){
		global $wpdb;
		$columns=$this->prepare_data_for_update($columns_data);
		
		$res=$wpdb->update($this->tablename, $columns, array('id' =>(int)$id));

		return $res;
	}
	public function update2($id,$columns_data){
		global $wpdb;
		$columns=$this->prepare_data_for_update($columns_data);
		
		$res=$wpdb->update($this->tablename, $columns, array('id' =>(int)$id));

		return var_export($columns,true);
	}
	//delete record
	public function remove_record($id){
		global $wpdb;
		$removed=$wpdb->delete($this->tablename,array('id' => (int)$id));
		return $removed;
	}
	//get field distinct
	public function get_by_field_unique($field){
		global $wpdb;
		$res=array();
		$sql="SELECT DISTINCT ".$field." FROM " . $this->tablename.' WHERE 1';
		$records = $wpdb->get_results($sql,ARRAY_A);
		if(!empty($records)){
			foreach($records as $val) $res[]=$val[$field];
		}
		return $res;

	}
	//add quotes
	
	public function add_quotes($val){
		return '"'.$val.'"';
	}
}