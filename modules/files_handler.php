<?php
class woordf_files_handler
{
	var $files;
	var $image_dim;
	var $sep;


	public function __construct($files)
	{

		$this->files=array();
		foreach($files as $key=>$file){
			$this->files[]=$this->filename_only($file);
		}
		$this->image_dim=$this->retrieve_img_dims($files[0]);
		$this->sep=$this->get_seperator();

	}
	public function get_thumbnail($path){
		$src=$path.$this->files[0];
		return '<img src="'.$src.'" />';
	}
	//filename only
	public function filename_only($file){
		$fname_parts=explode("/",$file);
		return $fname_parts[count($fname_parts)-1];
	}
	public function get_ext($file_name)
	{
		$array = explode(".", $file_name);
		$ext = $array[1];
		return $ext;
	}
	public function get_name($file_name)
	{

		$array = explode(".", $file_name);
		$name = $array[0];
		return $name;
	}
	
	//exist y axis 
	
	public function y_axis_exist(){
		$exist=false;
		$sep=$this->sep;
		foreach($this->files as $key=>$file){
			$pos = strrpos($file,$sep."y".$sep);
			if($pos!==false){
				 $exist=true;
				 break;
			}
		}
		return $exist;
	}
	//get seperator 
	
	public function get_seperator(){
		$pos = strrpos($this->files[0], "-");
		if($pos!==false){
			 return '-';
		}
		else{
			return '_';
		}
	}
	//get y filename 
	public function get_y_file(){
		$sep=$this->sep;
		foreach($this->files as $key=>$file){
			$pos = strrpos($file, $sep."y".$sep);
			if($pos!==false){
				 return $file;
				 break;
			}
		}
		return '';
	}
	//get filename
	
	public function get_filename($axis='x'){
		if($axis=='x'){
			$file=$this->files[0];
		}
		else{
			$file=$this->get_y_file();
		}
		$ext=$this->get_ext($file);
		$parts=explode($this->sep,$file);
		if(is_array($parts) && count($parts)>1){
			array_pop($parts);
			if(is_array($parts) && count($parts)){
				return implode($this->sep,$parts).$this->sep.'{index}.'.$ext;
			}
			else return $parts.$this->sep.'{index}.'.$ext;
		}
		else return '';

	}

	//get files count
	
	public function get_files_count($axis='x'){
		$count=0;
		$sep=$this->sep;
		if($axis=='x'){
			foreach($this->files as $key=>$file){
				$pos = strrpos($file, $sep."y".$sep);
				if($pos===false){
					 $count++;
				}
			}
		}
		else{
			foreach($this->files as $key=>$file){
				$pos = strrpos($file, $sep."y".$sep);
				if($pos!==false){
					 $count++;
				}
			}
		}

		return $count;
	}
	
	//retrieve_img_dims
	public function retrieve_img_dims($file){
		list($width, $height, $type, $attr) = getimagesize($file);

		return ($width && $height)?array('width'=>$width,'height'=>$height):array();
	}
	
	//get_image_dimensions
	
	public function get_image_dimensions(){
		return $this->image_dim;
	}
}
?>