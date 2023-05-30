<?php
class woordf_records_module extends woordf_db_module
{
    private static $instance;
    
    public function __construct() { 
        parent::__construct();
        
        $this->setTableName(WORDF_UPLOADS_TABLE);
        $this->setFields( array(
        	'id'=>array('type'=>'int','default'=>''),
        	'category'=>array('type'=>'int','default'=>1),
        	'type'=>array('type'=>'int','default'=>0), 
        	'user_id'=>array('type'=>'int'), 
        	'order_id'=>array('type'=>'int'),
        	'remote'=>array('type'=>'int','default'=>1),
        	'vip'=>array('type'=>'int','default'=>0),
        	'confirmed'=>array('type'=>'int','default'=>0),
        	'folder_name'=>array('type'=>'string','default'=>''),
        	'attributes'=>array('type'=>'string','default'=>''),
        	'label'=>array('type'=>'string','default'=>''),
        	'expires'=>array('type'=>'datetime','default'=>woordf_get_datetime('','Y-m-d H:i:s','+30 day')),
        	'expire_enabled'=>array('type'=>'int','default'=>0),
        	'updated'=>array('type'=>'datetime','default'=>woordf_get_datetime()),
        	'created'=>array('type'=>'datetime','default'=>woordf_get_datetime()),
        ) );

    }
    
    //table users
    
	public function get_table_users(){
		global $wpdb;
		$sql="SELECT DISTINCT user_id FROM " . $this->tablename." WHERE 1";
		$records = $wpdb->get_results($sql,ARRAY_A);	
		return $records;
	}

	
	//orders
	public function get_table_orders(){
		global $wpdb;
		$sql="SELECT DISTINCT order_id FROM " . $this->tablename." WHERE 1";
		$records = $wpdb->get_results($sql,ARRAY_A);
		$res=array();
		if(!empty($records)){
			foreach($records as $item) $res[]=$item['order_id'];
		}	
		return $res;
	}
	
	//remove record and folder with contents

	public function empty_folder($dir,$remove_folder=true)
	{
		if (is_dir($dir))
		{
			$objects = scandir($dir);

			foreach ($objects as $object)
			{
				if ($object != '.' && $object != '..')
				{
					if (filetype($dir.'/'.$object) == 'dir') {$this->empty_folder($dir.'/'.$object,true);}
					else {unlink($dir.'/'.$object);}
				}
			}

			reset($objects);
			if($remove_folder) rmdir($dir);
		}
	}
	public function remove($id){
		global $wpdb;
		$record=$this->get_where(array('id'=>(int)$id));
		if($record && $record[0]['folder_name']!=''){
			$resource=WORDF_UPLOADS_FOLDER.'/'.$record[0]['folder_name'];
			if(is_dir($resource)){
				$this->empty_folder($resource);
			}
		}
		
		return $this->remove_record((int)$id);

	}
	public function empty_folder_content($folder_name){

		if($folder_name!=''){
			$resource=WORDF_UPLOADS_FOLDER.'/'.$folder_name;
			if(is_dir($resource)){
				$this->empty_folder($resource,false);
			}
		}

	}
	//remove expired 
	public function remove_expired(){
		global $wpdb;
		$res=array();
		$results = $wpdb->get_results("SELECT * FROM " . $this->tablename." WHERE expire_enabled = 1 AND  (expires < CURDATE())",ARRAY_A);
		if(!empty($results)){
			foreach($results as $record){
				$date_expire=woordf_get_datetime($record['expires'],'Y-m-d');
				$label=($record['label']!='')?' ('.$record['label'].')':'';
				$comment=sprintf(__('Removed expired record %s and files, expire date: %s',WORDF_LANG), '#'.$record['id'].$label,$date_expire);
				$res[]=array('record_id'=>$record['id'],'user_id'=>$record['user_id'],'order_id'=>$record['order_id'],'label'=>$label,'expires'=>$record['expires'],'comment'=>$comment,'value'=>2,'type'=>'report');
				$this->remove($record['id']);

			}
		}
		return $res;
	}
	
	//returns log data of record whitch soon expires
	public function get_soon_expires_data($days_before){
		global $wpdb;
		$days_before=(!$days_before)?4:((int)$days_before+1);
		$res=array();

		$results = $wpdb->get_results("SELECT * FROM " . $this->tablename." WHERE expire_enabled = 1 AND (expires < CURDATE() + INTERVAL ".$days_before." DAY)",ARRAY_A);
		$today=woordf_get_datetime('','Y-m-d');
		
		if(!empty($results)){
			foreach($results as $record){
				$date=woordf_get_datetime('','Y-m-d H:i');
				$days_difference=woordf_get_days_difference($today,$record['expires']);
				$date_expire=woordf_get_datetime($record['expires'],'Y-m-d');
				$label=($record['label']!='')?' ('.$record['label'].')':'';
				$comment=sprintf(__('Record %s will expire after %s days, on: %s',WORDF_LANG), '#'.$record['id'].$label,$days_difference,$date_expire);
				$res[]=array('record_id'=>$record['id'],'user_id'=>$record['user_id'],'order_id'=>$record['order_id'],'label'=>$label,'expires'=>$record['expires'],'comment'=>$comment,'value'=>1,'type'=>'report');

			}
		}
		return $res;
	}
}