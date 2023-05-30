
<div class="woordf-wrapper">
<?php woordf_output_styled_success_checkmark();?>
<h1><?php _e('Upload record preview and edit',WORDF_LANG);?></h1>
<div class="irow">
	<a href="<?php echo admin_url( 'admin.php?page='.WORDF_ADMIN_PAGE_SLUG); ?>" class="button button-primary back"><?php woordf_output_icon('angle-left');_e('Back to list',WORDF_LANG);?></a>
</div>


<?php
if(isset($record)){
	$fields=woordf_admin_input_fields();
	$fields_js='';
	foreach($fields as $key=>$field){
		if($fields_js!='') $fields_js.=',';
		$fields_js.='{name:"'.$field['name'].'",value:"'.$record[$field['name']].'"}';
	}
	
	
	
	$is_remote=$record['remote'];
	$is_360=($record['type']==3);
	$folder_name=$record['folder_name'];
	$files=(!$is_remote)?woord_directory_handler::get_folder_content_list($folder_name):array();
	$files_size=woord_directory_handler::get_dir_size($folder_name);
	$user_info = ($record['user_id']!='' && $record['user_id']!=0)?get_userdata($record['user_id']):false;
	$name=($user_info && isset($user_info->user_login))?$user_info->user_login:'-';
	$type=$record['type'];
	$vip=$record['vip'];
	
    $data= array(
            'id'    => $record['id'],
            'order'	=>($record['order_id']==0)?'-':$record['order_id'],
			'user'  => $name,
			'type'  => woordf_get_type_label($record['type']),
			'folder_name'=>$record['folder_name'],
			'label'=>$record['label'],
			'remote'=>woordf_get_label_by_val('remote',$record['remote']),
			'vip'=>woordf_get_label_by_val('vip',$record['vip']),
			'confirmed'=>woordf_get_label_by_val('confirmed',$record['confirmed']),
			'expires'=> woordf_get_datetime($record['expires'],'Y-m-d'),
			'updated'   => woordf_get_datetime($record['updated'],'Y-m-d H:i:s'),
			'created'   => woordf_get_datetime($record['created'],'Y-m-d H:i:s'),
     );
     if($record['remote']==1){
     	$attr_data=maybe_unserialize($record['attributes']);
	 	$data['file_url']=(isset($attr_data['preview']))?$attr_data['preview']:'';
	 	unset($data['folder_name']);
	 }
    $labels = woordf_admin_record_tabel_labels();
    unset($labels['actions']);

	

	if($type==3){
		$files=woord_directory_handler::get_folder_content_list($folder_name,'img');

		if(!empty($files)){
			$attributes=woordf_get_primary_atributes($files);
			$attributes=woordf_add_custom_attributes($attributes,$record['attributes']);
			$is_options=false;
			include WORDF_DIR.'/views/admin/parts/image_360.php';
		}
		else _e('No 360 photos was found.',WORDF_LANG); 
		?><br /><hr /><h2><?php _e('Record details',WORDF_LANG);?></h2><?php
	}
	?>
	<div class="folder_space" title="<?php _e('Folder contents size',WORDF_LANG);?>"><?php woordf_output_icon('harddrives'); echo ' '.$files_size;?></div>
	
	<div class="details_list"><?php
		foreach($data as $key=>$val){
			?><div class="item">
				<div><?php echo (isset($labels[$key]))?$labels[$key] : '';?></div>
				<div><?php echo ($val!='')?$val:'-';?></div>
			</div><?php
		}
	?></div>
	
	<?php
	if($record['order_id']==0){
?>
		<input type="hidden" autocomplete="off" class="input_field" name="order_id" value="">
		<div class="irow vip">
			<h2><?php _e('This record not assigned to any order.',WORDF_LANG);?></h2>
			<a href="#" class="show_order_selection button button-primary"><?php _e('Choose order to assign',WORDF_LANG);?></a>
			<div id="order_selection" style="display:none;"><?php woordf_admin_output_row_content('order_id');?></div>
		</div>
<?php
	}
	?>
		<div class="irow vip">
			<label><?php _e('Is this Premium?',WORDF_LANG);?></label>
			<div class="select-box" data-target="#record_vip">
				<div class="sbitem<?php echo ((int)$record['vip']==0)?' selected':'';?>" data-val="0"><?php woordf_output_icon('file');_e('Standart',WORDF_LANG);;?></div>
				<div class="sbitem vp<?php echo ((int)$record['vip']==1)?' selected':'';?>" data-val="1"><?php woordf_output_icon('star');_e('Premium',WORDF_LANG);?></div>
			</div>
			<input type="hidden" value="<?php echo $record['vip'];?>" id="record_vip" autocomplete="off" />
		</div>
		<div class="irow label">
			<label><?php _e('Label',WORDF_LANG);?></label>
			<input type="text" id="record_label" value="<?php echo $record['label'];?>" placeholder="" />	
		</div>
		<div id="enable_expiration_toogler" class="irow wcheckbox">
			<label class="title"><?php _e('Remove the post and its associated files after the expiration date',WORDF_LANG);?></label>
			<div class="styled-toogler"><input type="checkbox" id="enable_expiration" autocomplete="off" <?php echo ($record['expire_enabled'])?' checked':'';?> /><label for="enable_expiration"></label></div>
		</div>
		<div id="expiration_date_wrapper" class="irow expires"<?php echo (!$record['expire_enabled'])?'style="display:none;"':'';?>>
			<label><?php _e('Expires date',WORDF_LANG);?></label>
			<input type="text" id="record_expires" class="date-picker req-field input" value="<?php echo woordf_get_datetime($record['expires'],'Y-m-d')?>" placeholder="" data-default="<?php echo woordf_get_datetime('','Y-m-d','+30 day');?>" data-attr="expires"/>	
		</div>
		<div class="irow categories">
			<label><?php _e('Category',WORDF_LANG);?></label>
			<?php woordf_categories_select($record['category']);?>
			<?php woordf_create_new_category_element();?>
		</div>
		<?php
		if($is_remote){
			$remote_data=maybe_unserialize($record['attributes']);
			?>
			<div id="add_remote" class="remote_wrapper">
				<div class="irow">
					<label><?php _e('Add files as url, from other server (cloud)',WORDF_LANG);?></label>
					<input type="text" id="remote_url" value="<?php echo (isset($remote_data['preview']))? $remote_data['preview']:'';?>" placeholder="" />
				</div>
				<div class="irow">
					<label><?php _e('Download file url',WORDF_LANG);?></label>
					<input type="text" id="remote_download_url" value="<?php echo (isset($remote_data['download']))? $remote_data['preview']:'';?>" placeholder="" />
				</div>
			</div>
			<?php
		}
		?>

	<?php

	if(!empty($files) && $type!=3){
		?><h2 class="wsp"><?php _e('The folder',WORDF_LANG);echo ' "'.$folder_name.'" ';_e('files',WORDF_LANG);echo ':'?></h2>
		<p class="tip"><?php echo __('Full path: ',WORDF_LANG).WORDF_UPLOADS_FOLDER.$folder_name;?></p>
		<?php
		$type=$record['type'];
    	$type_slug=woordf_get_type_slug($type);
    	$video_files=woord_directory_handler::get_folder_content_list($folder_name,'video');
		include_once WORDF_DIR.'views/admin/modals.php';
		
		?><div class="files_list"><?php
		$ind=0;
		foreach($files as $key=>$file){
			$fname=woordf_filename_only($file);
			$valid_image=woordf_valid_image($file);
			include WORDF_DIR.'/views/admin/loop_grid_item.php';
		}
		
		if($type!=1){
			$files=woordf_remove_invalid_images($files);
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
		?></div><?php

	}
	else if(empty($files) && !$is_remote) _e('Folder is empty',WORDF_LANG);

	if(!$is_remote){
		?>

		<h2><?php  
			if($type!=3){
				_e('There you can add aditional files',WORDF_LANG);
			}
			else{
				_e('Replace files',WORDF_LANG);
			}
			
			?></h2>
		<div class="woordf-row-wrapper">
			<div id="upload_wrapper" class="upl">

				<div id="fileuploader"><?php _e('Upload',WORDF_LANG);?></div>
				<div id="eventsmessage"></div>
				
			</div>
			<div class="response_message"></div>
		</div>

		<?php
	}
	?>
	<div class="woordf-row-wrapper sm">
			<label><?php _e('Preview url:',WORDF_LANG);?></label>
			<?php 
			?><a href="<?php echo woordf_get_file_embed_url($folder_name);?>" target="_blank"><?php echo woordf_get_file_embed_url($folder_name);?></a>
	</div>
	<div class="woordf-row-wrapper sm">
		<div class="response_message"></div>
		<a href="#" class="button button-primary update"><?php woordf_output_icon('save');_e('Update',WORDF_LANG);?></a>
	</div>
	<br /><hr />
	<div class="woordf-row-wrapper sm">
		<h2><?php _e('Archyve files',WORDF_LANG);?></h2>
		<div class="response_message"></div>
		<a href="#" class="button button-secondary zip_files"><?php woordf_output_icon('clip');_e('Archyve files for upload',WORDF_LANG);?></a>
	</div>
	<?php if($vip):?>
	<div id="record_logs">
		<?php
		if(isset($record_logs) && !empty($record_logs)){
			

			woordf_output_product_logs($record_logs);
			
			//if(!$record['confirmed']){
			?>
			<br /><hr />
			<div id="admin_comments_wrapper" class="woordf-row-wrapper">
				<h2><?php _e('After you upload new files, and make changes - comment and let user rate product again.',WORDF_LANG);?></h2>
				<div class="admin-comments">
					<label><?php _e('Write your comment',WORDF_LANG);?></label>
					<textarea class="admin_comment" autocomplete="off"></textarea>
					<div class="error-message" style="display:none;"> <?php _e('Please enter the comment!',WORDF_LANG);?></div>
					<a href="" class="button button-primary rate-product"><?php woordf_output_icon('check-box');_e('Send',WORDF_LANG);?></a>
				</div>
				<div class="after-rate" style="display:none;"></div>
			</div>
			<?php
			//}

		}
		else _e('User not rated this product yet.',WORDF_LANG);
		?>
	</div>
	<?php endif;?>
	<?php

}
else _e('An error occured, record was not found!',WORDF_LANG);
			
?>

</div>	
<script>
jQuery(function($){
      $.datepicker.regional['lt'] = {
      			clearText: 'Išvalyti', clearStatus: '',
                closeText: 'UŽdaryti', closeStatus: '',
                prevText: '&lt;Atgal',  prevStatus: '',
                nextText: 'Pirmyn&gt;', nextStatus: '',
                currentText: "&#352;iandien", currentStatus: '',
                monthNames: ['Sausis','Vasaris','Kovas','Balandis','Gegu&#382;&#279;','Bir&#382;elis','Liepa','Rugpj&#363;tis','Rugs&#279;jis','Spalis','Lapkritis','Gruodis'],
                monthNamesShort: ['Sau','Vas','Kov','Bal','Geg','Bir',
                'Lie','Rugp','Rugs','Spa','Lap','Gru'],
                monthStatus: '', yearStatus: '',
                weekHeader: '', weekStatus: '',
                dayNames: ['sekmadienis','pirmadienis','antradienis','tre&#269;iadienis','ketvirtadienis','penktadienis','&#353;e&#353;tadienis'],
                dayNamesShort: ['sek','pir','ant','tre','ket','pen','&#353;e&#353;'],
                dayNamesMin: ['Sk','Pr','An','Tr','Kt','Pn','&#352;t'],
                dayStatus: 'DD', dateStatus: 'D, M d',
                dateFormat: 'yy-mm-dd', firstDay: 1,
				timeOnlyTitle: 'Pasirinkite laiką',
				timeText: 'Laikas',
				hourText: 'Valanda',
				minuteText: 'Minutė',
				secondText: 'Sekundė',
				timezoneText: 'Laiko juosta',
                initStatus: '', isRTL: false};
        $.datepicker.setDefaults($.datepicker.regional['lt']);
});
(function($){
	var output_dir="<?php echo $folder_name;?>";
	var fields=[<?php echo $fields_js;?>];
	var current_record_id=<?php echo $record['id']?>;
	var paramaters_updated=false;
	var attrs={};
	var expire_enabled_value=0;
	
	//show uploader
	
	function woordf_show_uploader(){
		if($('#upload_wrapper').length){
			var wrapper=$('.woordf-wrapper');
			currently_uploading=0;
			var attr_data={
				action:'woordf_upload_files',
				filename:'myfile',
				remote:0,
				expire_enabled: expire_enabled_value,
				folder_name:output_dir,
				type:<?php echo $type;?>
			}
			attr_data=woordf_append_attr_from_data(attr_data);
			var allowed_extensions=woordf_allowed_extensions();

			$("#fileuploader").uploadFile({
				url:"<?php echo admin_url('admin-ajax.php')?>",
				formData:attr_data,
				fileName:"myfile",
				uploadStr:"<?php _e('Upload',WORDF_LANG);?>",
				dragDropStr: "<span><b><?php _e('Drag &amp; Drop Files',WORDF_LANG);?> ("+allowed_extensions+")</b></span>",
				multiDragErrorStr: "<?php _e('Multiple File Drag &amp; Drop is not allowed.',WORDF_LANG);?>",
	            allowedTypes: allowed_extensions,
	            extErrorStr: "<?php _e('Wrong file format, allowed: ',WORDF_LANG);?>",
	            statusBarWidth: 600,
	            dragdropWidth: 600,
	            showCancel: false,
	            showAbort: false,
	            showProgress: true,
	            allowDuplicates: false,
	            onSelect:function(files)
				{
					uploading_files_count=files.length;
				},
				onSubmit:function(files)
				{
					currently_uploading++;
				},
				onSuccess:function(files,data,xhr)
				{
					if(currently_uploading==uploading_files_count){

		            	wrapper.addClass('success');
		            	wrapper.find('.success_message').html('<span>'+uploading_files_count+' '+"<?php _e('file(-s) was attached successfully!',WORDF_LANG);?>");
		 				setTimeout(function(){location.reload()},800);
					}	
				}
			});
		}
	}
	//is future date
	function woordf_is_future_date(date){
		var today = new Date().getTime();
		var entered = new Date(date).getTime();
		return (today < entered);
	}
	//attr form data to append
	function woordf_append_attr_from_data(data){
		$.each(fields , function(i, val) { 
	  		data[val.name]=val.value;
		});
		return data;
	}
	function add360_view_update() {
		const viewId='image360';
		const el=$('#'+viewId);
		el.removeAttr('class');
		el.html('');
		el.addClass('cloudimage-360');


		var obj={};
		$( '#woordf_360_preview .attr-input' ).each(function() {
			var th=$(this);
			var attribute_name=th.attr('data-attribute');
			var target=th.attr('data-target');
			var static_value=th.attr('data-static_value');
			var val;
			if(attribute_name && attribute_name!=''){
				attribute_name=attribute_name.replace(/"|'/g, '');
				if(target && $(target).length) val=$(target).val()
				else val=(static_value)?static_value : attribute_name;

				if(th.hasClass('checkbox')){
					if(th.is( ":checked" )){
						el.attr(attribute_name,val);
					}
					else{
						el.removeAttr(attribute_name);
					}
				}
				else{
					val=th.val();
					if(!th.parent().hasClass('inv')){
						el.attr(attribute_name,val);
					}
					else{
						el.removeAttr(attribute_name);
						
					}
				}
			}
		});

		window.CI360.add(viewId);
	}
	
	
	function add360_update_hidden(){
		$( '#woordf_360_preview .attr-input' ).each(function() {
			var th=$(this);
			var target=th.attr('data-target');
			var checked=th.is( ":checked" );
			if(target && $(target).length){
				if(th.hasClass('checkbox')){
					var element_show=th.attr('data-element_show');
					if(checked){
						if($(target).parent().hasClass('inv')) $(target).parent().removeClass('inv');
						if($(element_show).length && $(element_show).hasClass('inv')) $(element_show).removeClass('inv');
				
					}
				}
			}
			var element_show=th.attr('data-element_show');
			if(element_show && th.hasClass('checkbox')){
				if(checked){
					if($(element_show).length){
						$(element_show).each(function() {
							if($(this).hasClass('inv')) $(this).removeClass('inv');
						});	
					}

				}
				else{
					if($(element_show).length){
						$(element_show).each(function() {
							if(!$(this).hasClass('inv')) $(this).addClass('inv');

						});	
					}
				}

			}
		});
	}
	function add360_get_parameters() {

		var obj={};
		var fields_data={};
		var pr_attrs={};
		$( '#woordf_360_preview .attr-input' ).each(function() {
			var th=$(this);
			var attribute_name=th.attr('data-attribute');
			var target=th.attr('data-target');
			var static_value=th.attr('data-static_value');
			var val;
			var field_val=th.val();
			var checked=false;
			if(th.hasClass('checkbox')){
				checked=th.is( ":checked" );
				field_val=(checked)? 1 : 0;
			}

			fields_data[th.attr('id')]=field_val;

			if(attribute_name && attribute_name!=''){
				attribute_name=attribute_name.replace(/"|'/g, '');
				if(target && $(target).length) val=$(target).val()
				else val=(static_value)?static_value : attribute_name;

				if(th.hasClass('checkbox')){
					if(checked && !th.parent().hasClass('inv')){
						pr_attrs[attribute_name]=val;
					}
				}
				else{
					val=th.val();
					if(!th.parent().hasClass('inv')){
						pr_attrs[attribute_name]=val;
					}
				}
			}
		});

		return {'attrs':pr_attrs,'fields':fields_data};
	}
	//show modal 
	
	function woordf_show_admin_modal(video_url){
		var woordf_admin_modal=$('#woordf_admin_modal');
		woordf_admin_modal.modal('show');
	}
	
	//alowed extensions 
	function woordf_allowed_extensions(){
		var type=<?= $record['type']; ?>;
		if(type==1) return "zip MP4 mp4 MOV mov";
		else if(type==3) return "zip";
		else return "zip png jpg jped gif";
	}


	//trash 
	$(document).on('click tap', ".files_list .item .icon-trash", function(event) {
		event.preventDefault();
		var th=$(this);
		var parent=th.closest('.item');

		parent.addClass('removing');
		setTimeout(function(){parent.fadeOut(300)},400);
		attr_data={
			action:'woordf_remove_local_file',
			folder_name:output_dir,
			file_name:parent.find('.it-inner').attr('data-file')
		}
        $.ajax({
            type: 'POST',
            url: "<?php echo admin_url('admin-ajax.php')?>",
            data: attr_data,
            success: function(resp) {
            	data = JSON.parse(resp)
                if(data.status==2) alert("<?php _e('Folder or file missing!',WORDF_LANG);?>");
                if(data.status==3) alert("<?php _e('Some error occured!',WORDF_LANG);?>");

            },
            error: function(err) {
                console.log(err)
            }
        });

    });

	$(document).on('click tap', ".woordf-wrapper .update", function(event) {
		event.preventDefault();
		var th=$(this);
		var wrapper=$('.woordf-wrapper');
		wrapper.addClass('loading');
		th.hide();
		
		<?php if($is_remote):?>
			var remote_url=$('#remote_url').val();
			var remote_download_url=$('#remote_download_url').val();
			attributes_obj={
				'preview':remote_url,
				'download':remote_download_url
			}
		<?php endif;?>
		
		attr_data={
			action:'woordf_record_update',
			record_id:<?php echo $record['id'];?>,
			label:$('#record_label').val(),
			vip:$('#record_vip').val(),
			expires:$('#record_expires').val(),
			file_name:$("#file_name").val(),
			expire_enabled: expire_enabled_value,
			<?php if($record['user_id']!=0):?>user_id:<?php echo $record['user_id'].',';?><?php endif;?>
			<?php if($is_remote):?>
			attributes:attributes_obj
			<?php endif;?>
		}
		$.each(attrs , function(i, val) { 
	  		attr_data[i]=val;
		});
		if($('[name="order_id"]').val()!='' && $('[name="order_id"]').val()!='0') attr_data['order_id']=$('[name="order_id"]').val();

        $.ajax({
            type: 'POST',
            url: "<?php echo admin_url('admin-ajax.php')?>",
            data: attr_data,
            success: function(resp) {
            	data = JSON.parse(resp);
            	//console.log(data);
            	wrapper.removeClass('loading').addClass('success');
            	wrapper.find('.success_message').html("<span><?php _e('Record was updated successfully!',WORDF_LANG);?></span>");
 				setTimeout(function(){location.reload()},800);

            },
            error: function(err) {
                console.log(err);
                wrapper.removeClass('loading');
                th.show();
            }
        });
    });
    //zip_files
	$(document).on('click tap', ".woordf-wrapper .zip_files", function(event) {//woordf-wrapper .save
		event.preventDefault();
		var th=$(this);
		var parent=th.closest('.woordf-row-wrapper');
		th.hide();
		parent.addClass('loading');
		attr_data={
			action:'woordf_archive_files',
			folder_name:"<?php echo $record['folder_name'];?>",
		}
        $.ajax({
            type: 'POST',
            url: "<?php echo admin_url('admin-ajax.php')?>",
            data: attr_data,
            success: function(resp) {
            	//data = JSON.parse(resp);
            	//console.log(data);
            	parent.removeClass('loading');
            	if(resp!='') parent.find('.response_message').html("<span><?php _e('Files archived successfully!',WORDF_LANG);?> "+resp+"</span>");
            	else parent.find('.response_message').html("<span><?php _e('Something went wrong!',WORDF_LANG);?></span>");
 				th.show();

            },
            error: function(err) {
                console.log(err);
                parent.removeClass('loading');
                th.show();
            }
        });
    });
    //vip
	$(document).on('click tap', ".vip .sbitem", function(event) {
		event.preventDefault();
		var th=$(this);
		var val=th.attr('data-val');
		$(th.parent().attr('data-target')).val(val);
		if($(".vip .sbitem.selected").length) $(".vip .sbitem.selected").removeClass('selected');
		th.addClass('selected');
    }); 
    


    //rate-product
    
	$(document).on('click tap', ".rate-product", function(event) {
		event.preventDefault();
		var th=$(this);
		var container=$('#admin_comments_wrapper');

		var admin_comment=container.find('.admin_comment').val();
		if(admin_comment=='') container.find('.admin-comments .error-message').fadeIn(300);
		else{
					
			container.addClass('loading');
			var attrs={
				action:'woordf_add_log',
				user_id:<?php echo get_current_user_id();?>,
				record_id:current_record_id,
				type:'admin',
				value:1,
				comment:admin_comment
			}


	        $.ajax({
	            type: 'POST',
	            url: "<?php echo admin_url('admin-ajax.php')?>",
	            data: attrs,
	            success: function(resp) {
					var data=JSON.parse(resp);
					$("#record_logs table tbody").append(data.content);
					container.removeClass('loading');
					container.find('.admin-comments').fadeOut(300);
	               	container.find('.after-rate').fadeIn(300).html('<p class="lg">'+data.message+'</p>');
					
	            },
	            error: function(err) {
	                container.removeClass('loading');
	                console.log(err)
	            }
	        });
		}
    });
	//order selection
	$(document).on('click tap', ".show_order_selection", function(event) {
		event.preventDefault();
		var th=$(this);
		if(!th.hasClass('active')) th.addClass('active').fadeOut(300);
		th.next().fadeIn(300);
    });
    //droplist option
	$(document).on('click tap', ".woordf-selector .option", function(event) {
		event.preventDefault();
		var th=$(this);
		var parent=th.parent();
		var val=th.attr('data-value');
		var selected=th.hasClass('selected');
		var target_name=parent.attr('data-target');
		if(parent.find('.option.selected').length) parent.find('.option.selected').removeClass('selected');
		if(!selected){
			th.addClass('selected');
			$('[name='+target_name+']').val(val).trigger('change');
		}

    });
	//update presentation parameters
	
	$(document).on('click tap', ".update_options", function(event) {
		event.preventDefault();
		var th=$(this);
		var parent=$("#woordf_360_preview");
		paramaters_updated=true;
		parent.addClass('loading');
		attr_data={
			action:'woordf_parameters_update',
			woordf_id:<?php echo $record['id'];?>,
			attributes:add360_get_parameters()
		}


        $.ajax({
            type: 'POST',
            url: "<?php echo admin_url('admin-ajax.php')?>",
            data: attr_data,
            success: function(resp) {
            	data = JSON.parse(resp);
            	//console.log(data);
            	parent.removeClass('loading');
            	$('.after_update').addClass('woordf-message woordf-success').fadeIn(300);

            	setTimeout(function(){
            		$('.after_update').fadeOut(300);
            	},4000);

 				//setTimeout(function(){location.reload()},800);

            },
            error: function(err) {
                console.log(err);

            }
        });
    });
    
	$('#woordf_admin_modal').on('hidden.bs.modal', function () {
	 	if(paramaters_updated) location.reload();
	});
    //on click options
     
	$(document).on('click tap', '#woordf_360_preview .attr-input', function(event) {
		var th=$(this);
		var target=th.attr('data-target');
		var ifchecked=th.attr('data-ifchecked'); 
		
		var checked=th.is( ":checked" );
		if(target && $(target).length){
			if(th.hasClass('checkbox')){
				if(checked){
					if($(target).parent().hasClass('inv')) $(target).parent().removeClass('inv');
				}
				else{
					if(!$(target).parent().hasClass('inv')) $(target).parent().addClass('inv');

				}
				
			}
			add360_view_update();
			
		}
		else{
			add360_view_update();
		}
		var element_show=th.attr('data-element_show');
		if(element_show && th.hasClass('checkbox')){
			if(checked){
				if($(element_show).length){
					$(element_show).each(function() {
						if($(this).hasClass('inv')) $(this).removeClass('inv');
					});	
				}

			}
			else{
				if($(element_show).length){
					$(element_show).each(function() {
						if(!$(this).hasClass('inv')) $(this).addClass('inv');

					});	
				}
			}

		}
		if(ifchecked && $(ifchecked).length){
			add360_view_update();
		}
		

	});	 
	// on change 
	$(document).on('change', '#woordf_360_preview .attr-input.select', function(event) {
		var th=$(this);
		var target=th.attr('data-target');
		var ifchecked=th.attr('data-ifchecked'); 

		if(ifchecked && $(ifchecked).length){
			add360_view_update();
		}
		

	});	
	$(document).on('change', '#woordf_360_preview .attr-input.number_text', function(event) {
		add360_view_update();
		console.log('tt');
	});	
    //enable_expiration
    
	$(document).on('click tap', '#enable_expiration_toogler .styled-toogler', function(event) {   	
		
		var target=$('#expiration_date_wrapper');
		var checked = $('#enable_expiration').prop('checked');
		expire_enabled_value=(checked)?1:0;
		
		if(checked){
			target.fadeIn(300);
			$('#record_expires').trigger('change');
		}
		else{
			target.fadeOut(300);
		}
    });
	$(document).on('change', '.woordf-wrapper .input', function(event) {
		event.preventDefault();
		attrs={	};
		$( '.woordf-wrapper .input' ).each(function() {
			var th=$(this);
			var attr=th.attr('data-attr');
			attrs[attr]=th.val();
		});
    });
	$(document).on('click tap', '.create-new-category', function(event) {
		event.preventDefault();
		var th=$(this);
		th.hide();
		$('#new_cat_wrapper').show();
    });
    //add category 
	$(document).on('click tap', "#add_category", function(event) {
		event.preventDefault();
		var th=$(this);
		var parent=$(".woordf-wrapper");
		var input_field=$("#new_category");
		var val=input_field.val();
		
		if(val!=''){
			if(input_field.hasClass('error')) input_field.removeClass('error');
			parent.addClass('loading');
			attr_data={
				action:'woordf_add_category',
				name: val,
				return_options:1,
			}


	        $.ajax({
	            type: 'POST',
	            url: "<?php echo admin_url('admin-ajax.php')?>",
	            data: attr_data,
	            success: function(resp) {
	            	data = JSON.parse(resp);
					if(data.status==1){
						parent.removeClass('loading');
						input_field.val('');
						$("#category").html(data.options).trigger('change');
						$('#new_cat_wrapper').fadeOut(300,function(){$('.create-new-category').show()});
					}

	            },
	            error: function(err) {
	                console.log(err);
	            }
	        });
		}
		else input_field.addClass('error');

    });
    
    //check date
    
	$(document).on('change', '#record_expires', function(event) {
		event.preventDefault();
		var is_future=woordf_is_future_date($(this).val());

		if(!is_future){
			$(this).val($(this).attr('data-default'));
			alert("<?php _e('Please enter future date!',WORDF_LANG);?>");
		}

		else woordf_show_uploader();

    });


	$(document).ready(function()
	{
		expire_enabled_value=($('#enable_expiration').prop('checked'))?1:0;
		<?php if($type!=3):?>
			var content="<?php include_once WORDF_DIR.'/views/parts/photoswipe_min.php'?>";
			$(document.body).prepend(content);
			
		<?php endif;?>
		woordf_show_uploader();
		$('.date-picker').datepicker($.extend({}, $.datepicker.regional['lt'], {
			dateFormat: 'yy-mm-dd'
		}));
	});
})(jQuery);	
</script>