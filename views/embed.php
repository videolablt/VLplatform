<?php


$endpoints=woordf_woo_my_account_endpoints();

$item=$endpoints[0];
$current_user_id=get_current_user_id();
$db_actions=new woordf_records_module();
$title=__($item['label'],WORDF_LANG);
$results=array();

$folder_name=get_query_var('record_id');
$folder_name=($folder_name!='')?$folder_name:'';

if($folder_name!=''){
	$results=$db_actions->get_where(array('folder_name'=>'"'.$folder_name.'"'));	
}	


		

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
<title><?php _e('Embed gallery',WORDF_LANG)?></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta http-equiv="cache-control" content="no-cache" />
<META NAME="ROBOTS" CONTENT="NOINDEX, NOFOLLOW">
<script src="https://code.jquery.com/jquery-3.2.1.min.js" integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4=" crossorigin="anonymous"></script>

<link rel="stylesheet" type="text/css" href="<?php echo WORDF_URL.'assets/css/bootstrap-part.css'?>">
<link rel="stylesheet" type="text/css" href="<?php echo WORDF_URL.'assets/css/normalize.css'?>">
<link rel="stylesheet" type="text/css" href="<?php echo WORDF_URL.'assets/css/embed-style.css'?>">
<?php
$cont_class='';
if(!empty($results)){

	$record=$results[0];
	$type=$record['type'];
	$cl_type=($type==1)?' video':' images';
	$cont_class=($record['type']==3)? ' photo-360':$cl_type;
		switch ($record['type']) {
			    case 2: //photos
			    	?><link rel="stylesheet" type="text/css" href="<?php echo WORDF_URL.'libs/photoswipe/photoswipe.css'?>"><?php
			    	?><link rel="stylesheet" type="text/css" href="<?php echo WORDF_URL.'libs/photoswipe/default-skin/default-skin.css'?>"><?php
			    	?><script src="<?php echo WORDF_URL.'libs/photoswipe/photoswipe.js'?>"></script><?php
			    	?><script src="<?php echo WORDF_URL.'libs/photoswipe/photoswipe-ui-default.min.js'?>"></script><?php

			        break;

		}
}

?>
</head>
<body class="embed">

					<?php
	if(isset($record)){


		switch ($type) {
		    case 1: //video
				$files=woord_directory_handler::get_folder_content_list($record['folder_name'],"video");

				if(!empty($files)){
					?><div id="woordf_view_files_modal"><div class="record-block"><?php
					include_once WORDF_DIR.'/views/parts/video.php';
					?></div></div><?php
				}
				else{
					?><div class="col-md-12"><h3><?php _e('No files found.',WORDF_LANG);?></h3></div><?php
				}
					
		        break;
		    case 2: //photos
		    ?>
<div class="container-fluid woordf_embeded<?php echo $cont_class;?>">
	<div class="row">
		    <?php
		    	$files=woord_directory_handler::get_folder_content_list($record['folder_name'],"img");
				if(!empty($files)){
					?><div class="col-md-12 woordf-photo-gallery"><?php
						include_once WORDF_DIR.'/views/parts/photoswipe.php';
						include_once WORDF_DIR.'/views/parts/image_gallery.php';
					?></div><?php
				}
				else{
					?><div class="col-md-12"><h3><?php _e('No files found.',WORDF_LANG);?></h3></div><?php
				}
	?>
	</div>
</div>
<?php	
		        break;
		    case 3: //360 / 3D photo
		    	$files=woord_directory_handler::get_folder_content_list($record['folder_name'],"img");
				if(!empty($files)){
					$attributes=woordf_get_primary_atributes($files);
					$attributes=woordf_add_custom_attributes($attributes,$record['attributes']);
					$files_handler=new woordf_files_handler($files);
					$image_dimensions=$files_handler->get_image_dimensions();
					include WORDF_DIR.'/views/parts/image_360.php';

				}
				else{
					?>
					<div class="container-fluid">
						<div class="row">
							<div class="col-md-12"><h3><?php _e('No files found.',WORDF_LANG);?></h3></div>
						</div>
					</div>	
							<?php
				}
		        break;

		}

	}
	else{

		    ?>
<div class="container-fluid woordf_embeded<?php echo $cont_class;?>">
	<div class="row">
		    <?php
		?><div class="col-md-12"><h2><?php _e('Sorry, there is no results found',WORDF_LANG);?></h2></div><?php
	?>
	</div>
</div>
<?php
	}

?>
</body>
</html>
