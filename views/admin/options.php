<?php
//echo get_option('blogname').'---'.get_option('siteurl');



function woordf_record_selector($records,$record_id){
	if(!empty($records)){
		
		?>
<form action="<?php echo admin_url( 'admin.php?'); ?>" method="GET" class="woordf_options_filter">
	<input type="hidden" name="page" value="<?php echo WORDF_OPTIONS_PAGE_SLUG;?>" />
	<label for="record_selector"><?php _e('Select 360 / 3D photo for testing',WORDF_LANG); ?></label>
	<input type="hidden" value="1" name="tab" />
	<select id="record_selector" name="record_id" autocomplete="off"><?php
	foreach($records as $key=>$record)
	{
		?><option<?php echo (($record_id==0 && $key==0) || ($record_id!=0 && $record_id==$record['id']))?' selected':'';?> value="<?=$record['id'];?>">#<?php echo $record['id'].' - '.$record['label'];?></option><?php
	}
	?></select>

	<?php submit_button( __('Use',WORDF_LANG),'primary',NULL);?>
   	
    	
</form>
<?php
	}
}
?>
<div id="woordf_options_form" class="wrap woordf-wrapper options-form">
	<h1><?php echo '<b>'.__("Woo order files",WORDF_LANG).' '.'</b> '.__('plugin options',WORDF_LANG); ?></h1>
    <div id="woordf-tabs">  
         <ul>  
            <li data-ind="0"><a href="#tabs-1" ><?php esc_html_e('Main options', WORDF_LANG);?></a></li>   
            <li data-ind="1"><a href="#tabs-2"><?php esc_html_e('Photo 360 / 3D default parameters', WORDF_LANG);?></a></li>  
         </ul>  
         <div id="tabs-1">  
			<form method="post" action="options.php">
				<?php 
				settings_errors();
				settings_fields('woordf_options');
				do_settings_sections('woordf_options'); 
    			$admin_options_fields=woordf_admin_options_fields();
    			$admin_options=new woordfAdmin_options_module($admin_options_fields);

				?>
				<h3><?php echo __('Main plugin', WORDF_LANG).' "'.__('Woo order files', WORDF_LANG).'" '.__('options', WORDF_LANG); ?></h3>
				
				<?php $admin_options->output_options_fields();?>
				<?php submit_button(); ?>
			</form>
		</div>
         <div id="tabs-2">  
				<h2><?php echo __('Choose default 360 / 3D photos parameters',WORDF_LANG); ?></h2>
			<?php
				$record_id=(isset($_GET['record_id']))?$_GET['record_id']:0;
				$db_actions=new woordf_records_module();
				$all_results=$db_actions->get_where(array('type'=>3,'remote'=>0));
				woordf_record_selector($all_results,$record_id);
				$woordf_id=(!empty($all_results) && $record_id==0)?$all_results[0]['id']:$record_id;
				$results=($woordf_id!=0)?$db_actions->get_where(array('id'=>$woordf_id)):array();		

				if(isset($results) && !empty($results)){
					
					$record=$results[0];
					$folder_name=$record['folder_name'];
					$files=woord_directory_handler::get_folder_content_list($folder_name,'img');
					
					if(!empty($files)){
						$attributes=woordf_get_primary_atributes($files);
						$attributes=woordf_add_default_attributes($attributes);

						$is_options=true;
						include WORDF_DIR.'/views/admin/parts/image_360.php';
					}
					else _e('No presentations found.',WORDF_LANG); 

					
				}
				else _e('No presentations found.',WORDF_LANG); 

			?>
		</div>
	</div>	



</div>
<script>

(function($){
	function woordf_change_url(page, title, url) {
		if ("undefined" !== typeof history.pushState) {
			history.pushState({page: page}, title, url);
		} else {
			window.location.assign(url);
		}
	}

	function add360_view_update() {
		const viewId='image360';
		const el=$('#'+viewId);
		el.removeAttr('class');
		el.html('');
		el.addClass('cloudimage-360');


		var obj={};
		$( '#woordf_options_form .attr-input' ).each(function() {
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
	
	//get parameters 
	
	function woordf_add360_get_parameters() {

		var obj={};
		var fields_data={};
		var pr_attrs={};
		$( '#woordf_options_form .attr-input' ).each(function() {
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
	
	//update hidden

	function add360_update_hidden(){
		$( '#woordf_options_form .attr-input' ).each(function() {
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
	
	//update presentation parameters
	
	$(document).on('click tap', ".update_options", function(event) {//woordf-wrapper .save
		event.preventDefault();
		var th=$(this);
		var parent=$("#woordf_options_form");
		paramaters_updated=true;
		parent.addClass('loading');
		attr_data={
			action:'woordf_default_parameters_update',
			attributes:woordf_add360_get_parameters()
		}


        $.ajax({
            type: 'POST',
            url: "<?php echo admin_url('admin-ajax.php')?>",
            data: attr_data,
            success: function(resp) {
            	data = JSON.parse(resp);

            	parent.removeClass('loading');
            	$('.after_update').addClass('woordf-message woordf-success').fadeIn(300);


 				setTimeout(function(){location.reload()},1000);

            },
            error: function(err) {
                console.log(err);

            }
        });
    });
	//in pop-up 

	$(document).on('click tap', '#woordf_options_form .attr-input', function(event) {
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
	$(document).on('change', '#woordf_options_form .attr-input.select', function(event) {
		var th=$(this);
		var target=th.attr('data-target');
		var ifchecked=th.attr('data-ifchecked'); 

		if(ifchecked && $(ifchecked).length){
			add360_view_update();
		}
		

	});	
	$(document).on('change', '#woordf_options_form .attr-input.number_text', function(event) {
		add360_view_update();
	});	
	//change url
	$(document).on('click tap', '.ui-tabs-anchor', function(event) {
		event.preventDefault();
		options_page_url="<?php echo admin_url( 'admin.php?page='.WORDF_OPTIONS_PAGE_SLUG.'&tab=');?>";
		var href=options_page_url+$(this).parent().attr('data-ind');
		woordf_change_url("page", "page", href);

	});
	$(document).ready(function()
	{
		add360_update_hidden();
		$( "#woordf-tabs" ).tabs({ 
				active: <?php echo (isset($_GET['tab']))?$_GET['tab']:'0'?>, 
		});  
	});
	
})(jQuery);	
</script>