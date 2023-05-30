<?php
// Ajax part
Class woocommerce_woordf_ajax
{
	public function __construct() {

		
		add_action('wp_ajax_woordf_upload_files',array(&$this,'upload_files'));
		add_action('wp_ajax_nopriv_woordf_upload_files',array(&$this,'upload_files'));
		
		add_action('wp_ajax_woordf_add_remote_file',array(&$this,'add_remote_file'));
		add_action('wp_ajax_nopriv_woordf_add_remote_file',array(&$this,'add_remote_file'));
		
		add_action('wp_ajax_woordf_remove_local_file',array(&$this,'remove_local_file'));
		add_action('wp_ajax_nopriv_woordf_remove_local_file',array(&$this,'remove_local_file'));
		
		add_action('wp_ajax_woordf_record_update',array(&$this,'record_update'));
		add_action('wp_ajax_nopriv_woordf_record_update',array(&$this,'record_update'));
		
		add_action('wp_ajax_woordf_archive_files',array(&$this,'archive_files'));
		add_action('wp_ajax_nopriv_woordf_archive_files',array(&$this,'archive_files'));
		
		add_action('wp_ajax_woordf_get_order_files',array(&$this,'get_order_files'));
		add_action('wp_ajax_nopriv_woordf_get_order_files',array(&$this,'get_order_files'));
		
		add_action('wp_ajax_woordf_get_record_files',array(&$this,'get_record_files'));
		add_action('wp_ajax_nopriv_woordf_get_record_files',array(&$this,'get_record_files'));
		
		add_action('wp_ajax_woordf_add_log',array(&$this,'add_log'));
		add_action('wp_ajax_nopriv_woordf_add_log',array(&$this,'add_log'));
		
		add_action('wp_ajax_woordf_admin_360_content',array(&$this,'admin_360_content'));
		add_action('wp_ajax_nopriv_woordf_admin_360_content',array(&$this,'admin_360_content'));
		
		add_action('wp_ajax_woordf_parameters_update',array(&$this,'parameters_update'));
		add_action('wp_ajax_nopriv_woordf_parameters_update',array(&$this,'parameters_update'));
		
		add_action('wp_ajax_woordf_default_parameters_update',array(&$this,'default_parameters_update'));
		add_action('wp_ajax_nopriv_woordf_default_parameters_update',array(&$this,'default_parameters_update'));

		add_action('wp_ajax_woordf_update_category',array(&$this,'update_category'));
		add_action('wp_ajax_nopriv_woordf_update_category',array(&$this,'update_category'));
		
		add_action('wp_ajax_woordf_add_category',array(&$this,'add_category'));
		add_action('wp_ajax_nopriv_woordf_add_category',array(&$this,'add_category'));
		
		add_action('wp_ajax_woordf_remove_category',array(&$this,'remove_category'));
		add_action('wp_ajax_nopriv_woordf_remove_category',array(&$this,'remove_category'));
	}
	//remove category
	public function remove_category(){
		$status=2;

		if(isset($_POST['id']) && $_POST['id']!='' && $_POST['id']!=0){
			
			$db_actions=new woordf_categories_module();
			
			$success=$db_actions->remove_record($_POST['id']);
			$status=($success)?1:2;

	
		}
		else $status=3;
		$message=($status==1)?__('Category was removed.',WORDF_LANG) : __('Some error occured!',WORDF_LANG);

		echo json_encode(array('status'=>$status,'message'=>$message));

		die();
	}
	
	//add category
	
	public function add_category(){
		$status=2;
		$table='';
		$options='';
		if(isset($_POST['name']) && $_POST['name']!=''){
			
			$db_actions=new woordf_categories_module();
			$not_exist=$db_actions->category_not_exist($_POST['name']);
			if($not_exist){
				$inserted_cat_id=$db_actions->insert_row(array('name'=>$_POST['name']));
				$status=($inserted_cat_id)?1:2;
			}
			else $status=4;
	
		}
		else $status=3;
		if($status==4){
			$message= __('A category with that name already exists!',WORDF_LANG);
		}
		else $message=($status==1)?__('Category was successfully added!',WORDF_LANG) : __('Some error occured!',WORDF_LANG);
		if($status==1){
			if(isset($_POST['return_options'])){
				ob_start();
				woordf_categories_select_options(array(),$inserted_cat_id);
				$options = ob_get_clean();
				
			}
			else if(isset($_POST['return_table'])){
				include_once WORDF_DIR.'/inc/admin_tables/woordf_categories_table.php';
				$category_table = new woordf_categories_table();
			    $category_table->prepare_items();
				ob_start();
				$category_table->display();
				$table = ob_get_clean();
			}
		}
		
		echo json_encode(array('status'=>$status,'message'=>$message,'table'=>$table,'options'=>$options));

		die();
	}
	//update category
	
	public function update_category(){
		$status=1;
		
		if(isset($_POST['id']) && $_POST['id']!='' && isset($_POST['name']) && $_POST['name']!=''){
			$db_actions=new woordf_categories_module();
			$not_exist=$db_actions->category_not_exist($_POST['name']);
			$success=true;;
			if($not_exist){
				
				$success=$db_actions->update((int)$_POST['id'],array('name'=>$_POST['name']));
				$status=($success)?1:2;
			}
			else $status=4;
			
			
		}
		else $status=3;
		if($status==4){
			$message= __('A category with that name already exists!',WORDF_LANG);
		}
		else $message=($status==1)?__('Category was successfully updated!',WORDF_LANG) : __('Some error occured!',WORDF_LANG);

		echo json_encode(array('status'=>$status,'message'=>$message));

		die();
	}
	//parameters update 
	public function parameters_update(){
		$status=1;
		$serialized=maybe_serialize($_REQUEST['attributes']);
		if(isset($_REQUEST['woordf_id']) && isset($_REQUEST['attributes'])){
			$id=(int)$_REQUEST['woordf_id'];
			$db_actions=new woordf_records_module();
			$db_actions->update($id,array('attributes'=>$serialized));

		}
		else $status=3;

		echo json_encode(array('status'=>$status));

		die();
	}
	
	//default params update
	public function default_parameters_update(){
		$status=1;
		
		if(isset($_POST['attributes'])){
			update_option('woordf_default_parameters',$_POST['attributes']);
			
		}
		else $status=3;

		echo json_encode(array('status'=>$status));

		die();
	}
	
	//in admin - 360 preview content 
	public function admin_360_content(){
		
	
		if(isset($_POST['record_id']) && $_POST['record_id']!=0){
			$record_id=(int)$_POST['record_id'];
        	$db_actions=new woordf_records_module();
			if($record_id!=0) $results=$db_actions->get_where(array('id'=>$record_id));		


			if(isset($results) && !empty($results)){
				
				$record=$results[0];
				$is_options=false;
				$folder_name=$record['folder_name'];
				$files=woord_directory_handler::get_folder_content_list($folder_name,'img');

				if(!empty($files)){
					$attributes=woordf_get_primary_atributes($files);
					$attributes=woordf_add_custom_attributes($attributes,$record['attributes']);

					include WORDF_DIR.'/views/admin/parts/image_360.php';
				}
				else _e('No files found.',WORDF_LANG); 

				
			}
			else _e('No files found.',WORDF_LANG); 
		


		}
		else _e('No files found.',WORDF_LANG); 
		
		die();
	}
	//add log 
	public function add_log(){
		$status=0;
		$message=__('Please enter the comment!',WORDF_LANG);
		$last_log='';
		if(isset($_POST['record_id']) && $_POST['record_id']!=0 && isset($_POST['type']) && $_POST['type']!='' && isset($_POST['comment']) && $_POST['comment']!=''){
			$record_id=(int)$_POST['record_id'];
        	$db_actions=new woordf_logs_module();
			$inserted=$db_actions->insert_row($_POST);
			$last_log=$db_actions->get_latest_log($record_id);
			$status=(!$inserted)?2:1;
			$message=(!$inserted)?__('Some error occured, please try later!',WORDF_LANG):__('You successfully rated product!',WORDF_LANG);
			$max_count_used=woordf_max_count_used($record_id);
			if(((int)$_POST['value']==1 && $_POST['type']!='admin') || ($_POST['type']=='admin') && $max_count_used){
				$db_actions2=new woordf_records_module();
				$db_actions2->update($record_id,array('confirmed'=>1));//confirmed vip record
			}
			$email_admin_after_customer_rates = get_option(WORDF_PX.'email_admin_after_customer_rates');
			if($_POST['type']=='rate' && $email_admin_after_customer_rates){
				$db_actions_records=new woordf_records_module();
				$record_results=$db_actions_records->get_where(array('id'=>(int)$_POST['record_id']));
				$record=$record_results[0];
				$user_info = get_userdata($record['user_id']);
				$name=($user_info)?$user_info->user_login:'-';
				$data=array('user_id'=>$record['user_id'],'customer'=>'#'.$record['user_id'].' ('.$name.')','record'=>'#'.$_POST['record_id'].' ('.$record['label'].')','order_id'=>$record['order_id'],'comment'=>$_POST['comment']);
				woordf_send_mail('admin','customer_rated',$data);
			}
			$email_customer_after_admin_rates = get_option(WORDF_PX.'email_customer_after_admin_rates');
			if($_POST['type']=='admin' && $email_customer_after_admin_rates){
				$db_actions_records=new woordf_records_module();
				$record_results=$db_actions_records->get_where(array('id'=>(int)$_POST['record_id']));
				$record=$record_results[0];
				$user_info = get_userdata($record['user_id']);
				$name=($user_info)?$user_info->user_login:'-';
				$data=array('user_id'=>$record['user_id'], 'customer'=>'#'.$record['user_id'].' ('.$name.')','record'=>'#'.$_POST['record_id'].' ('.$record['label'].')','order_id'=>$record['order_id'],'comment'=>$_POST['comment']);
				woordf_send_mail('customer','admin_rated',$data);
			}

		}


		echo json_encode(array('status'=>$status,'message'=>$message,'content'=>woordf_get_log_row($last_log)));
		die();
	}

	//get record files 
	public function get_record_files(){
		if(isset($_POST['record_id']) && $_POST['record_id']!=0){
			$record_id=(int)$_POST['record_id'];
			$db_actions=new woordf_records_module();
			$user_id=get_current_user_id();
			$results=$db_actions->get_where(array('user_id'=>$user_id,'id'=>$record_id),"AND");
			if(empty($results)){
				_e('No files found.',WORDF_LANG);
			}
			else{
				$record=$results[0];
				$type=$record['type'];
				$ftype=($type==1)?'video':'img';
				$type_slug=woordf_get_type_slug($type);
				$files=woord_directory_handler::get_folder_content_list($record['folder_name'],$ftype);
				if($type!=1) $files=woordf_remove_invalid_images($files);
				$label=__('Images',WORDF_LANG);
				if($type==1) $label=__('Videos',WORDF_LANG);
				if($type==3) $label=__('360 / 3D photo',WORDF_LANG);
				$label.=($record['label']!='')?' ('.$record['label'].')':'';
				$suffix=($type==3)?' '.__('frames',WORDF_LANG):'';
				$suffix=($type==1)?' '.__('video',WORDF_LANG):$suffix;
				$can_rate=false;
				$admin_made_changes=false;
				if($record['vip']){
					$db_logs_actions=new woordf_logs_module();
					$record_logs=$db_logs_actions->get_logs($record['id']);
					$admin_made_changes=$db_logs_actions->admin_made_changes($record['id']);
					$can_rate=(!$record['confirmed'] && $admin_made_changes);
					$max_rate_count_used=woordf_max_count_used($record['id']);
				}

				?>
				<div class="back-to-list"><?php woordf_output_icon('angle-left'); _e('Back to list',WORDF_LANG);?></div>
				<p class="title"><?php echo $label.'&nbsp;('.count($files).$suffix.')';?></p>
				<div class="embed-file" style="display:none;"><div class="inner"><?php woordf_output_embed_content($record);?></div></div>
				<div class="rate-record" style="display:none;">
					<div class="wrapper">
						<div class="coll form rate">
							<div class="inner">
								<p class="lg"><?php _e('Product rating',WORDF_LANG);?></p>
								
								<?php if($can_rate):?>
								<div class="woordf-rate">
									<div class="option good" data-value="1">
										<div class="check"><?php woordf_output_icon('check');?></div>
										<div class="label"><?php woordf_output_icon('thumb-up');_e('The quality satisfies me',WORDF_LANG);?></div>
									</div>
									<div class="option bad" data-value="0">
										<div class="check"><?php woordf_output_icon('check');?></div>
										<div class="label"><?php woordf_output_icon('thumb-down');_e('I have comments',WORDF_LANG);?></div>
									</div>
								</div>
								<div class="user-comments" style="display:none;">
									<label><?php _e('Write your comment',WORDF_LANG);?></label>
									<textarea class="user_comment"></textarea>
									<div class="error-message" style="display:none;"> <?php _e('Please enter the comment!',WORDF_LANG);?></div>
									<a href="" class="btn button btn-primary rate-product" data-user="<?php echo get_current_user_id();?>"><?php _e('Rate',WORDF_LANG);?></a>
								</div>
								<?php 
								else:
									?><div class="rate-status"><?php
									if($record['confirmed']) echo woordf_output_icon('check-box',__('Confirmed',WORDF_LANG),false).' '.__('Product confirmed!',WORDF_LANG);
									else if($max_rate_count_used) echo woordf_output_icon('alert',__('Not',WORDF_LANG),false).' '.__('Max rating count is used',WORDF_LANG).': '.woordf_get_rate_count_used($record['id']);
									else  _e('Administrator not maded changes yet.',WORDF_LANG);
									?></div><?php
								endif;?>
							</div>
						</div>
						<div class="coll form after-rate" style="display:none;">
							<?php woordf_output_styled_success_checkmark();?>
						</div>
						<?php if(isset($record_logs)):?>
						<div class="coll logs">
							<div class="inner">
								<?php
								
								woordf_output_product_logs($record_logs,false);
								
								if(empty($record_logs)){
									 ?><p class="empty-table-label"><?php _e('This product not rated yet.',WORDF_LANG);?></p><?php
								}
								?>
							</div>
						</div>
						<?php endif;?>
					</div>
				</div>
				<div class="<?php echo ($type_slug=='photos')?'woordf-grid':'woordf-single';echo ' '.$type_slug;?>">
					<?php
					if($type==3){
						$attributes=woordf_get_primary_atributes($files);
						$attributes=woordf_add_custom_attributes($attributes,$record['attributes']);
						$files_handler=new woordf_files_handler($files);
						$image_dimensions=$files_handler->get_image_dimensions();
					}
					if($type==2){
						foreach($files as $key=>$file){
							include WORDF_DIR.'/views/loop/grid_item_'.$type_slug.'.php';	 
						}
					}
					else{
						include WORDF_DIR.'/views/parts/'.$type_slug.'.php';
					}
					?>
				</div>
				<div class="toolbar"><?php 
					woordf_output_download_button($record);
					woordf_output_embed_button($record);
					woordf_output_rate_button($record);
				?></div>
				<?php
				if($type==2){
					?>
					<script>
					
					var wp_openPhotoSwipe = function(ind) {
					    var pswpElement = document.querySelectorAll('.pswp')[0];
					    var items = [<?php woordf_output_file_info_for_js($files);?>];

					    var options = {       
					        history: false,
					        focus: false,
							index:ind,
							zoomEl: true,
							clickToCloseNonZoomable: false,
					        showAnimationDuration: 0,
					        hideAnimationDuration: 0
					        
					    };
					    
					    var gallery = new PhotoSwipe( pswpElement, PhotoSwipeUI_Default, items, options);
					    gallery.init();
					};		
					 (function($){  
							$(document).on('click', '.gallery_item', function(event) {
								event.preventDefault();
								var ind=parseInt($(this).attr('data-index'));
								wp_openPhotoSwipe(ind);
							});	

					})(jQuery);
					</script><?php
					
				}

			}	
		}
		die();
	}
	//get order files 
	public function get_order_files(){
		if(isset($_POST['order_id']) && $_POST['order_id']!=0){
			$order_id=(int)$_POST['order_id'];
			$records=woordf_user_order_downloads($order_id);

			if(!empty($records)){
				?>

				<div class="panel wait-block" style="display:none"><div class="top loading"></div><p class="message"><?php echo __('Files are being archived, it may take some time, please wait.',WORDF_LANG);?></p></div>
				<div class="panel record-block" style="display:none"></div>
				<div class="panel all-products-block active">
				<p class="title"><?php echo __('The order',WORDF_LANG).' #'.$order_id.' '.__('files',WORDF_LANG);?></p><?php
				?><div class="woordf-grid"><?php
				foreach($records as $record){
					include WORDF_DIR.'/views/loop/grid_item.php';
				}
				?></div>
				</div>
				<?php
			}
			else _e('No files found.',WORDF_LANG);
		}
		die();
	}
	//zip files
	public function archive_files(){
		if(isset($_REQUEST['folder_name'])){

			$archive_file=WORDF_UPLOADS_FOLDER.$_REQUEST['folder_name'].'/atsisiuntimui.zip';
			$rootPath = WORDF_UPLOADS_FOLDER.$_REQUEST['folder_name'].'/';

			$zip = new ZipArchive();
			$zip->open($archive_file, ZipArchive::CREATE | ZipArchive::OVERWRITE);


			$files = new RecursiveIteratorIterator(
			    new RecursiveDirectoryIterator($rootPath),
			    RecursiveIteratorIterator::LEAVES_ONLY
			);


			$files_glob=woord_directory_handler::get_folder_files($_REQUEST['folder_name'],'media');
			$filtered_files=array();
			foreach($files_glob as $fl){
				$filtered_files[]=substr($fl, strlen($rootPath));
			}
			foreach ($files as $name => $file)
			{
			    // Skip directories
			    if (!$file->isDir())
			    {
			        $filePath = $file->getRealPath();
			        $relativePath = substr($filePath, strlen($rootPath));

			        // Add current file to archive
			        if(in_array($relativePath,$filtered_files)) $zip->addFile($filePath, $relativePath);
			    }
			}

			$zip->close();
			$url=WORDF_UPLOADS_FOLDER_URL.$_REQUEST['folder_name'].'/atsisiuntimui.zip';
			if(isset($_POST['return_link'])) echo $url;
			else echo '<a href="'.$url.'">'.$url.'</a>';
		}
		die();
	}
	//update record
	public function record_update(){
		$status=1;

		if(isset($_REQUEST['record_id'])){
			$id=(int)$_REQUEST['record_id'];
			$db_actions=new woordf_records_module();
			
			$col_data=$_POST;

			if(isset($col_data['attributes'])) $col_data['attributes']=maybe_serialize($col_data['attributes']);
			
			$db_actions->update($id,$col_data);

		}
		else $status=3;

		echo json_encode(array('status'=>$status));

		die();
	}

	//remove_local_file
	public function remove_local_file(){

		$status=1;
		$file='';
		if(isset($_REQUEST['folder_name']) && isset($_REQUEST['file_name'])){
			$output_dir=(isset($_REQUEST['folder_name']))?WORDF_UPLOADS_FOLDER.$_REQUEST['folder_name'].'/' : WORDF_UPLOADS_FOLDER;
			$file=$output_dir.$_REQUEST['file_name'];

			if(!is_dir($output_dir) || !file_exists($file)){
				$status=2;
			}
			else{
				unlink($file);
			}

		}
		else $status=3;

		echo json_encode(array('status'=>$status));

		die();
	}
	//remote
	public function add_remote_file(){
		include_once WORDF_DIR.'/modules/upload_handler.php';
		$status=1;

		$db_actions=new woordf_records_module();
		$col_data=$_POST;

		if(!isset($col_data['label']) || $col_data['label']=='') $col_data['label']=woordf_get_file_name('',$col_data['attributes']);
		$col_data['attributes']=maybe_serialize($col_data['attributes']);
		$inserted=$db_actions->insert_row($col_data);
		$status=(!$inserted)?2:1;

		$edit_url=admin_url( 'admin.php?page='.WORDF_ADMIN_PAGE_SLUG.'&record='.$inserted);

		echo json_encode(array('status'=>$status,'redir'=>$edit_url));

		die();
	}
	//upload files localy
	public function upload_files(){
		include_once WORDF_DIR.'/modules/upload_handler.php';
		$status=1;
		$first_upload=false;
		$input_name='';
		$output_dir='';
		$edit_url='';

		if(isset($_POST['zip_only'])){
			$input_name=(isset($_REQUEST['filename']))?$_REQUEST['filename'] : 'myfile';
			$folder_name = woord_directory_handler::generate_random_string();
			$output_dir=WORDF_UPLOADS_FOLDER.$folder_name.'/';
			$created=woord_directory_handler::create_dir($output_dir);
			

			if(isset($_FILES[$input_name]))
			{
				//file_put_contents(dirname(__FILE__).'/testas_file.txt', var_export($_FILES[$input_name],true));
				$upload_handler=new woordf_upload_handler($output_dir,$input_name);
				$upload_handler->handle_file_upload();
			}

			$db_actions=new woordf_records_module();
			$col_data=$_POST;
			$col_data['folder_name']=$folder_name;
			$col_data['label']=woordf_get_file_name($col_data);
			if((int)$_POST['type']==3) $col_data['attributes']=woordf_get_default_attributes();


			$inserted=$db_actions->insert_row($col_data);
			
			$status=(!$inserted)?3:1;
			
			$edit_url='';
			if(isset($inserted)) $edit_url=admin_url( 'admin.php?page='.WORDF_ADMIN_PAGE_SLUG.'&record='.$inserted);

		}

		else{
			
			$output_dir=(isset($_REQUEST['folder_name']))?WORDF_UPLOADS_FOLDER.$_REQUEST['folder_name'].'/' : WORDF_UPLOADS_FOLDER;
			if(!is_dir($output_dir)){
				$created=woord_directory_handler::create_dir($output_dir);
				$first_upload=true;
			}
			else $created=true;
			if(isset($_REQUEST['type']) && (int)$_REQUEST['type']==3){
				$db_actions=new woordf_records_module();
				$db_actions->empty_folder_content($_REQUEST['folder_name']);
			}

			if($created){
				$input_name=(isset($_REQUEST['filename']))?$_REQUEST['filename'] : 'myfile';
				if(isset($_FILES[$input_name]))
				{
					$upload_handler=new woordf_upload_handler($output_dir,$input_name);
					$upload_handler->handle_file_upload();
				}
			}
			else $status=2;
			
			if($first_upload){
				$db_actions=new woordf_records_module();
			
				$inserted=$db_actions->insert_row($_POST);
				
				$status=(!$inserted)?3:1;
			}
			$edit_url='';
			if(isset($inserted)) $edit_url=admin_url( 'admin.php?page='.WORDF_ADMIN_PAGE_SLUG.'&record='.$inserted);
		}

		echo json_encode(array('status'=>$status,'items'=>$input_name,'output_dir'=>$output_dir,'redir'=>$edit_url));

		die();
	}

	
}
new woocommerce_woordf_ajax();