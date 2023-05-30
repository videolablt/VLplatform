<?php


$folder_name = woord_directory_handler::generate_random_string();


?>

<h1><?php _e('Upload new files',WORDF_LANG);?></h1>

<div id="woordf_new" class="woordf-wrapper">
	<?php woordf_output_styled_success_checkmark(true);?>
	<div id="message" class="woordf-message" style="display:none;"></div>
		<form>

		<div id="woordf_accordion">
			<?php
			$fields=woordf_admin_input_fields();
			$fields_js='';
			foreach($fields as $key=>$field){
				if($fields_js!='') $fields_js.=',';
				$fields_js.='{name:"'.$field['name'].'",value:""}';
				?>

				<div class="item" id="<?php echo 'id_'.$field['name'];?>">
					<div class="heading"><div><?php echo $field['label'];woordf_admin_output_row_heading_values($field['name']);?></div><?php woordf_output_icon('angle-down');?></div>
					<div class="content" style="display:none">
						<input type="hidden" autocomplete="off" class="input_field" name="<?php echo $field['name'];?>" value="<?php echo (isset($_GET[$field['name']]) && $_GET[$field['name']]!='')?$_GET[$field['name']]:'';?>">
						<?php woordf_admin_output_row_content($field['name']);?>
					</div>
				</div>
				<?php
			}
			
			?>
				<div class="item" id="id_upload">
					<div class="heading"><div><?php _e('Upload',WORDF_LANG);?></div><?php woordf_output_icon('angle-down');?></div>
					<div class="content" style="display:none">
						<input type="hidden" class="input_field" name="upload" value="<?php echo (!isset($_GET['vip']))?'':'1'?>" autocomplete="off">
						<div id="upload_wrapper" class="upl">
							<div class="irow vip">
								<label><?php _e('Is this Premium?',WORDF_LANG);?></label>
								<div class="select-box" data-target="#record_vip">
									<div class="sbitem<?php echo (!isset($_GET['vip']))?' selected':''?>" data-val="0"><?php woordf_output_icon('file');_e('Standart',WORDF_LANG);?></div>
									<div class="sbitem vp<?php echo (isset($_GET['vip']))?' selected':''?>" data-val="1"><?php woordf_output_icon('star');_e('Premium',WORDF_LANG);?></div>
								</div>
								<input type="hidden" value="<?php echo (!isset($_GET['vip']))?'0':'1'?>" id="record_vip" />
							</div>
							<div class="irow vip">
								<label><?php _e('Upload files localy or from other server?',WORDF_LANG);?></label>
								<div class="select-box" data-target="#is_remote">
									<div class="sbitem selected" data-val="0"><?php _e('Localy',WORDF_LANG);?></div>
									<div class="sbitem vp" data-val="1"><?php _e('From other server',WORDF_LANG);?></div>
								</div>
								<input type="hidden" value="0" id="is_remote" />
							</div>
							<div id="add_remote" class="remote_wrapper" style="display:none;">
								<div class="irow">
									<label><?php _e('Add files as url, from other server (cloud)',WORDF_LANG);?></label>
									<input type="text" id="remote_url" value="" placeholder="" />
								</div>
								<div class="irow">
									<label><?php _e('Download file url',WORDF_LANG);?><span class="tip"><?php _e(' (not required, can add later)',WORDF_LANG);?></span></label>
									<input type="text" id="remote_download_url" value="" placeholder="" />
								</div>
								<div class="irow">
									<label><?php _e('Label',WORDF_LANG);?><span class="tip"><?php _e(' (not required, can add later)',WORDF_LANG);?></span></label>
									<input type="text" id="remote_label" value="" placeholder="" />
								</div>
							</div>

							<div id="enable_expiration_toogler" class="irow wcheckbox">
								<label class="title"><?php _e('Remove the post and its associated files after the expiration date',WORDF_LANG);?></label>
								<div class="styled-toogler"><input type="checkbox" id="enable_expiration" autocomplete="off" /><label for="enable_expiration"></label></div>
							</div>
							<div id="expiration_date_wrapper" class="irow expires" style="display:none;">
								<label><?php _e('Expires date',WORDF_LANG);?></label>
								<input type="text" data-attr="expires" id="record_expires" class="date-picker req-field input" value="<?php echo woordf_get_datetime('','Y-m-d','+30 day')?>" data-default="<?php echo woordf_get_datetime('','Y-m-d','+30 day');?>" placeholder="" />	
							</div>
							<div class="irow categories">
								<label><?php _e('Category',WORDF_LANG);?></label>
								<?php woordf_categories_select();?>
								<?php woordf_create_new_category_element();?>
							</div>
							<div class="photo_360_part" style="display:none">
								<div class="instructions">
									<div class="toogler"><div class="iicon"><?php woordf_output_icon('info');?></div><?php _e('File format and name requirements.',WORDF_LANG);?><div class="iarrow"><?php woordf_output_icon('angle-down');?></div></div>
									<div class="info" style="display:none">
										<p><b><?php _e('Filne name structure:',WORDF_LANG);?></b> name_1.png, name_2.png. <?php _e('Mostly important seperator',WORDF_LANG);?> <b>"_"</b> <?php _e('between name ant frame',WORDF_LANG);?></p>
										<p><b><?php _e('Filne name with y axis structure:',WORDF_LANG);?></b> name_y_1.png, name_y_2.png. <?php _e('Mostly important seperator',WORDF_LANG);?> <b>"_y_"</b> <?php _e('between name ant frame',WORDF_LANG);?></p>
									</div>
								</div>
							</div>
							<div class="irow add-remote-btn" style="display:none;">
								<a href="#" class="button button-primary add_remote"><?php _e('Add',WORDF_LANG);?></a>
							</div>
							<div id="add_localy">
								<div class="irow lb">
									<label><?php _e('Add local',WORDF_LANG);?></label>
								</div>
								<div id="fileuploader"><?php _e('Upload',WORDF_LANG);?></div>
								<div id="eventsmessage"></div>
							</div>
	
						</div>
						
					</div>
				</div>	
		</div>

		</form>


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
	var container=$("#woordf_new");
	var fields=[<?php echo $fields_js;?>];
	var all_inputs_filled=false;
	woordf_update_data();
	var uploading_files_count=0;
	var currently_uploading=0;
	var expire_enabled_value=0;
	var attrs={};
	//show uploader

	function woordf_show_uploader(){
		$("#fileuploader").html('');
		currently_uploading=0;
		var redir_url='';
		var file_outputs;
		var drag_drop_label="<?php _e('Drag &amp; Drop Files',WORDF_LANG);?> (*.zip)";
		var wrapper=$('.woordf-wrapper');

	
		var attr_data={
			action:'woordf_upload_files',
			filename:'myfile',
			remote:0,
			vip:$('#record_vip').val(),
			expires:$('#record_expires').val(),
			zip_only:1,
			expire_enabled: expire_enabled_value,
		}
		attr_data=woordf_append_attr_from_data(attr_data);
		console.log(attr_data);



		$("#fileuploader").uploadFile({
			url:"<?php echo admin_url('admin-ajax.php')?>",
			formData:attr_data,
			fileName:"myfile",
			uploadStr:"<?php _e('Upload',WORDF_LANG);?>",
			dragDropStr: "<span><b>"+drag_drop_label+"</b></span>",
			multiDragErrorStr: "<?php _e('Multiple File Drag &amp; Drop is not allowed.',WORDF_LANG);?>",
            allowedTypes: "zip",
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
				

			},
			onSuccess:function(files,response,xhr)
			{
				//$("#eventsmessage").html($("#eventsmessage").html()+"<br/>"+files);
				currently_uploading++;
	            data = JSON.parse(response);
	            if(data.redir) redir_url=data.redir;
	             
				if(currently_uploading==uploading_files_count){

	                file_outputs=$('.ajax-file-upload-container').html();
	                $('#files_output').html(file_outputs);

	                if($('.ajax-file-upload-container').innerHeight()>200){
	                	if(!$('#files_output').hasClass('scroll')) $('#files_output').addClass('scroll');
	                }
	                else if($('#files_output').hasClass('scroll')) $('#files_output').removeClass('scroll');
		            wrapper.addClass('success');
		            wrapper.find('.success_message').html(uploading_files_count+' '+"<?php _e('file(-s) was attached successfully!',WORDF_LANG);?>");
	                if(redir_url){
						 $('.woordf-go-edit').attr('href',redir_url).show();
					}
					else $('.woordf-go-edit').hide();
					
				}
				else console.log(currently_uploading+' / '+uploading_files_count+' N ');
 
				
					
			}
		});
		
	}
	//check options
	function woordf_check_options(){
		var target_name,val,last_input;
		var th,parent,action,selected,target_name,curr_th;
		$( '.woordf-selector' ).each(function() {
			var th=$(this);
			target_name=th.attr('data-target');
			val=($('[name='+target_name+']').length)?$('[name='+target_name+']').val():'';
			if(val!=''){
				th.find( '.option' ).each(function() {
					var this_val=$(this).attr('data-value');
					if(this_val==val){	
						curr_th=$(this);
						parent=curr_th.parent();
						action=curr_th.attr('data-action');
						selected=curr_th.hasClass('selected');
						last_input=$('[name='+target_name+']');

						woordf_do_select_action(target_name,action);
					}

				});
			}
		});
		if(last_input) last_input.trigger('change');

	}
	//do acrion 
	function woordf_do_select_action(target_name,action){
		if(target_name=='type'){
			

			if(action=='image_360'){
				$('.photo_360_part').show(300);
			}
			else{
				$('.photo_360_part').hide(300);
			}
		}
		woordf_show_uploader();

	}
	//check accordion
	function woordf_check_accordion(){
		var ind=0;
		$( '#woordf_accordion .item' ).each(function() {
			var val=$(this).find('.input_field').val();
			if(ind>0 && val==''){
				
				$(this).addClass('disabled');
			}
			if(val!='') ind++;
			
		});
		woordf_update_data();
		woordf_manage_acordion(ind);
		if($('[name="download"]').val()!='') $('[name="download"]').trigger('change');
	}
	//update data
	function woordf_update_data(){
		$.each(fields , function(i, val) { 
	  		val.value=$( "[name="+val.name+"]" ).val();
		});
	}
	//attr form data to append
	function woordf_append_attr_from_data(data){
		$.each(fields , function(i, val) { 
	  		data[val.name]=val.value;
		});
		$.each(attrs , function(i, val) { 
	  		data[i]=val;
		});
		return data;
	}
	//accordion management
	function woordf_manage_acordion(filled){
		var ind=0;
		var th;
		$( '#woordf_accordion .item' ).each(function() {
			ind++;
			th=$(this);
			
			if(th.hasClass('disabled') && filled>=(ind-1)) th.removeClass('disabled');
			if(!th.hasClass('disabled') && filled<(ind-1)) th.addClass('disabled');
			if(filled==0 && ind==1){
				if(!th.hasClass('open')) th.addClass('open');
				th.find('.content').slideDown(300);
			}
			else if(filled==0 && ind>1){
				if(th.hasClass('open')) th.removeClass('open');
				th.find('.content').slideUp(300);
			}
			if(filled>0 && filled==(ind-1)){
				if(!th.hasClass('open')) th.addClass('open');
				th.find('.content').slideDown(300);
			}
			else if(filled>0 && ind!=(ind-1)){
				if(th.hasClass('open')) th.removeClass('open');
				th.find('.content').slideUp(300);
			}
		});
	}

	//is future date
	function woordf_is_future_date(date){
		var today = new Date().getTime();
		var entered = new Date(date).getTime();
		return (today < entered);
	}
	

	


	$(document).on('change', '#woordf_new form .input_field', function(event) {
		event.preventDefault();
		woordf_update_data();
		var el=$('#upload_wrapper');
		var fields_filled=0;
		$.each(fields , function(i, val) { 
	  		if(val.value!='') fields_filled++;
		});
		woordf_manage_acordion(fields_filled);


    });
    
	$(document).on('change', '.req-field', function(event) {
		event.preventDefault();
		var val=$(this).val();
		var id=$(this).attr('id');
		all_inputs_filled=woordf_check_inputs();
		
		if(all_inputs_filled){
			$("#fileuploader").removeClass('waiting');
			woordf_show_uploader();
		}
		else{
			$("#fileuploader").addClass('waiting');

			
		}
    });

	$(document).on('change', '#is_remote', function(event) {
		event.preventDefault();
		var val=$(this).val();
		var is_360=($('[name=type]').val()==3);
		if(val==1){
			$("#add_remote").slideDown(300);
			$("#add_localy").slideUp(300);
			$('.add-remote-btn').slideDown(300);
			if(is_360) $('.photo_360_part').slideUp(300);
			
		}
		else{
			$("#add_remote").slideUp(300);
			$('.add-remote-btn').slideUp(300);
			$("#add_localy").slideDown(300);
			if(is_360) $('.photo_360_part').slideDown(300);
		}
    });
    function woordf_check_inputs(){
    	var filled=true;
    	var check_fields=['#record_expires'];
    	if($("#is_remote").val()==1) check_fields.push('#remote_url');
    	
		$.each(check_fields , function(i, val) { 
	    	if($(val).val()==''){
				filled=false;
				if(!$(val).hasClass('empty')) $(val).addClass('empty');
			}
			else if($(val).hasClass('empty')) $(val).removeClass('empty');

		});
    	
		return filled;
	}
	$(document).on('click tap', '.add_remote', function(event) {
		event.preventDefault();
		all_inputs_filled=woordf_check_inputs();
		if(all_inputs_filled){
			var remote_url=$('#remote_url').val();
			var remote_download_url=$('#remote_download_url').val();
			var remote_label=$('#remote_label').val();
			attributes_obj={
				'preview':remote_url,
				'download':remote_download_url
			}
			
			container.addClass('loading');
			if($('#remote_url').hasClass('empty')) $('#remote_url').removeClass('empty');			
			woordf_update_data();
			var attr_data={
				action:'woordf_add_remote_file',
				remote:1,
				vip:$('#record_vip').val(),
				expires:$('#record_expires').val(),
				attributes:attributes_obj
			}
			if(remote_label!='') attr_data['label']=remote_label;
			attr_data=woordf_append_attr_from_data(attr_data);
	        $.ajax({
	            type: 'POST',
	            url: "<?php echo admin_url('admin-ajax.php')?>",
	            data: attr_data,
	           // dataType: 'json',
	            success: function(response) {
	                data = JSON.parse(response);

	                $("#message").html("<?php _e('File was attached successfully!',WORDF_LANG);?>").addClass('woordf-success').slideDown(300);
	                $("html, body").animate({scrollTop: 10},500);
	                container.removeClass('loading');
	                if(data.redir){
						 $('.woordf-go-edit').attr('href',data.redir).slideDown(300);
					}
	            },
	            error: function(err) {
	            	container.removeClass('loading');
	                console.log(err)
	            }
	        });

		}
		else console.log('not all');


    });
    //accordion 
	$(document).on('click tap', '#woordf_accordion .heading', function(event) {
		event.preventDefault();
		var current_item=$(this).parent();
		var current_id=current_item.attr('id');
		$( '#woordf_accordion .item' ).each(function() {

			var th=$(this);
			var th_id=th.attr('id');

			if(th_id==current_id){
				if(!th.hasClass('open')){
					th.addClass('open');
					th.find('.content').slideDown(300);
				}
				else{
					th.removeClass('open');
					th.find('.content').slideUp(300);
				}
			}
			else{
				th.removeClass('open');
				th.find('.content').slideUp(300);
			}
			
			
		});
    });
    //droplist option
	$(document).on('click tap', ".woordf-selector .option", function(event) {
		event.preventDefault();
		var th=$(this);
		var parent=th.parent();
		var heading_info=parent.parent().parent().find('.heading div span');
		var val=th.attr('data-value');
		var action=th.attr('data-action');
		var selected=th.hasClass('selected');
		var target_name=parent.attr('data-target');
		if(parent.find('.option.selected').length) parent.find('.option.selected').removeClass('selected');
		if(!selected){
			th.addClass('selected');
			$('[name='+target_name+']').val(val).trigger('change');
		}
		woordf_do_select_action(target_name,action);
		heading_info.html(th.text()).addClass('not-empty');

    });
	$(document).on('click tap', ".select-box .sbitem", function(event) {
		event.preventDefault();
		var th=$(this);
		var parent=th.parent();
		var val=th.attr('data-val');
		$(th.parent().attr('data-target')).val(val).trigger('change');
		if(parent.find(".sbitem.selected").length) parent.find(".sbitem.selected").removeClass('selected');
		th.addClass('selected');
    });
//change nput value 
	$(document).on('change', '.woordf-wrapper .input', function(event) {
		event.preventDefault();
		$( '.woordf-wrapper .input' ).each(function() {
			var th=$(this);
			var attr=th.attr('data-attr');
			attrs[attr]=th.val();
		});
		woordf_show_uploader();


    });
//instructions 
	$(document).on('click tap', '.instructions .toogler', function(event) {
		event.preventDefault();
		var th=$(this);
		var target=th.next();
		if(!target.hasClass('active')){
			target.addClass('active').slideDown(300);
			th.addClass('active');
		}
		else{
			target.removeClass('active').slideUp(300);
			th.removeClass('active');
		}


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

    //enable_expiration
	$(document).on('click tap', '#enable_expiration_toogler .styled-toogler', function(event) {   	
		var checked = $('#enable_expiration').prop('checked');
		var target=$('#expiration_date_wrapper');

		expire_enabled_value=(checked)?1:0;
		woordf_show_uploader();
		
		if(checked){
			target.fadeIn(300);
			$('#record_expires').trigger('change');
		}
		else{
			target.fadeOut(300);
		}
    });
	//new category creation 
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
	$(document).ready(function()
	{
		woordf_show_uploader();
		woordf_check_accordion();
		woordf_check_options();
		$('.date-picker').datepicker($.extend({}, $.datepicker.regional['lt'], {
			dateFormat: 'yy-mm-dd'
		}));
	
	});
})(jQuery);	
</script>