<?php
class woordf_upload_handler
{
	var $path;
	var $file_name;
	var $log;
	public function __construct($path="uploads/", $filename="filename")
	{

		$this->path=$path;
		$this->file_name=$filename;
		$this->log='';
	}
	public function get_ext($file_name)
	{
		$array = explode(".", $file_name);
		$ext = $array[count($array)-1];
		return $ext;
	}
	public function get_name($file_name)
	{

		$array = explode(".", $file_name);
		$name = $array[0];
		return $name;
	}
	public function handle_file_upload()
	{
		$file_name = $this->file_name;
		if (!is_array($_FILES[$file_name]["name"])) {
			$this->upload_file($_FILES[$file_name]["name"]);
		} else {
			$fileCount = count($_FILES[$file_name]["name"]);
			for ($i=0; $i < $fileCount; $i++) {
				$this->upload_file($_FILES[$file_name]["name"][$i],$i);
			}
		}
	}
	public function upload_file($file_name, $index=0)
	{
		$ext = $this->get_ext($file_name);
		$name= $this->get_name($file_name);
		$path = $this->path;

		$location =  $path . $file_name;
		$temp_file_loc=($index==0)?$_FILES[$this->file_name]['tmp_name'] : $_FILES[$this->file_name]['tmp_name'][$index];
		
		if ($ext == 'zip') {

			if (move_uploaded_file($temp_file_loc, $location)) {
				$zip = new ZipArchive;
				if ($zip->open($location)) {
					$zip->extractTo($path);
					$zip->close();
				}
				unlink($location);
			}
		} else {
			move_uploaded_file($temp_file_loc, $location);
		}
		$this->log.='Uploaded: '.$location.'<br>';
	}

	//log
	public function output_log()
	{
		return $this->log;
	}
}
?>