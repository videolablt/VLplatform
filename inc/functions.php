<?php
//datetime 
function woordf_get_datetime($date='',$format='Y-m-d H:i:s',$add=''){
	if($date!='') $datetime = new DateTime($date);
	else $datetime = new DateTime();
	$tzone = new DateTimeZone('Europe/Vilnius');
	//$datetime->setTimezone($tzone); 
	if($add!=''){
		$datetime = $datetime->modify( $add );
	}
    return $datetime->format($format);
}
//days difference
function woordf_get_days_difference($today,$later){
	$datetime1 = new DateTime($today);

	$datetime2 = new DateTime($later);

	$difference = $datetime1->diff($datetime2);

	return ((int)$difference->d);

}

//meta 

function woordf_get_meta($post_id,$field,$alt='')
{
	$opt=get_post_meta( $post_id, $field, true );
	return (! empty($opt))? $opt: $alt;
}

//table users
function woordf_get_table_users(){
	$result=array();
	$db_actions=new woordf_records_module();
	$users=$db_actions->get_table_users();
	if(!empty($users)){
		foreach($users as $user){
			$user_info = get_userdata($user['user_id']);
			$result[$user['user_id']]=($user_info)?$user_info->user_login:'-';
		}
	}
	return $result;
}

//orders 
function woordf_get_table_orders(){
	$result=array();
	$db_actions=new woordf_records_module();
	$orders=$db_actions->get_table_orders();

	return $orders;
}

function woordf_get_categories(){
	
	$db_actions_categories=new woordf_categories_module();
	$db_actions=new woordf_records_module();
	$using_cats=$db_actions->get_by_field_unique('category');
	$results=$db_actions_categories->get_table_data(array('id'=>'ASC'));
	$res=array();
	if(!empty($results)){
		foreach($results as $category){
			if(in_array($category['id'],$using_cats)) $res[$category['id']]=$category['name'];
		}
	}
	return $res;
}
//file name 

function woordf_get_file_name($col_data,$from_url=''){
	if($from_url==''){
		$file_key=$col_data['filename'];
		$filename=((isset($_FILES[$file_key]["name"]) && $_FILES[$file_key]["name"]!=''))?$_FILES[$file_key]["name"]:'';
		$fname_parts=explode(".",$filename);
		
	}
	else{

		$fname_parts=explode(".",basename($from_url));
	}
	return $fname_parts[0];

}
//output orders 
function woordf_output_orders_options(){
	$args = array(
	    'status' => array('wc-processing', 'wc-on-hold','wc-pending'),
	);
	$field_name='order_id';
	$orders=wc_get_orders($args);
	if(!empty($orders)){
			?>
			<div class="option" data-value="0">
				<div class="check"><?php woordf_output_icon('check');?></div>
				<div class="text"><?php _e('Not link to any order/user',WORDF_LANG);?></div>
			</div><?php
		foreach($orders as $order){
			?>
			<div class="option<?php woordf_selected($field_name,$order->ID);?>" data-value="<?php echo $order->ID;?>">
				<div class="check"><?php woordf_output_icon('check');?></div>
				<div class="text"><?php echo '#'.$order->ID.' '.$order->get_billing_first_name().' ('.woordf_get_datetime($order->get_date_created()).')';?></div>
			</div><?php
		}

	}
}

//output types 
function woordf_output_type_options(){
	$types=woordf_get_types();

	$field_name='type';
	foreach($types as $key=>$val){
		?><div class="option" data-value="<?php echo $key;?>" data-action="<?php echo $val['action'];?>">
				<div class="check"><?php woordf_output_icon('check');?></div>
				<div class="text"><div class="inner"><?php woordf_output_icon(woordf_icon_by_type_and_key($field_name,$key));?><?php echo $val['label'];?></div></div>
		</div><?php
	}
}

//if selected
function woordf_selected($key,$val){
	echo (isset($_GET[$key]) && $_GET[$key]!='' && $_GET[$key]==$val)?' selected':'';
}

//button class 
function woordf_upl_class(){
	echo (isset($_GET['order']) && $_GET['order']!='' && isset($_GET['type']) && $_GET['type']!='' && isset($_GET['download']) && $_GET['download']!='')?'':' woordf-disabled';
}
//select class 
function woordf_select_class($key){
	echo (isset($_GET[$key]) && $_GET[$key]!='' && $_GET[$key]!='0')?'':' class="empty"';
}



//output icon 
function woordf_output_icon($icon,$title='',$echo=true){
	if(!$echo) return Woostify_Icon::fetch_svg_icon( $icon,$echo,$title );
	else Woostify_Icon::fetch_svg_icon( $icon,$echo,$title );
}

//my downloads table data 
function woordf_user_downloads(){
	$db_actions=new woordf_records_module();
	$user_id=get_current_user_id();
	$results=$db_actions->get_where(array('user_id'=>$user_id));
	return $results;
}
//user order downloads
function woordf_user_order_downloads($order_id){
	$db_actions=new woordf_records_module();
	$user_id=get_current_user_id();
	$results=$db_actions->get_where(array('user_id'=>$user_id,'order_id'=>$order_id),"AND");
	return $results;
}
//get orders information
function woordf_get_user_orders_list(){
	$db_actions=new woordf_records_module();
	$user_id=get_current_user_id();

	$data=$db_actions->get_where(array('user_id'=>$user_id),"AND",array(),array('order_id','DESC'));
	$results=array();
	$order_ids=array();
	if(!empty($data)){
		foreach($data as $item){
			$oid=$item['order_id'];
			$type=$item['type'];
			if(!in_array($oid,$order_ids)){
				
				$order_ids[]=$oid;
				$results[$oid]=array('date'=>woordf_get_order_date($oid),'types'=>array($type));
			}
			else{
				if(isset($results[$oid]['types']) && !in_array($type,$results[$oid]['types'])) $results[$oid]['types'][]=$type;
			}
		}
	}
	
	return $results;
}

//order date 

function woordf_get_order_date($order_id){
	$order = wc_get_order( $order_id );
	return $order->get_date_created();
}

//order url

function woordf_get_view_order_url($order_id){
	$order = wc_get_order( $order_id );
	return $order->get_view_order_url();
}

//output order product types 
function woordf_output_order_product_types($types){
	foreach($types as $key=>$type){
		if($key>0) echo ', ';
		echo '<span>'.woordf_get_type_label($type).'</span>';
	}
}
//output type 
function woordf_get_type_label($key){
	$types=woordf_get_types();
	return (isset($types[$key]))?$types[$key]['label']:$types[1]['label'];
}

//get file url 
function woordf_get_single_file_url($record){

	$url=WORDF_UPLOADS_FOLDER_URL.$record['folder_name'].'/'.$record['file_name'];
	return $url;
}

//preview url 

function woordf_get_file_embed_url($id){
	$args = array(
	    'post_type'  => 'page', 
	    'meta_query' => array( 
	        array(
	            'key'   => '_wp_page_template', 
	            'value' => 'embed.php '
	        )
	    )
	);
	$preview_post=get_posts($args);
	//return esc_url( wc_get_account_endpoint_url( $endpoint ).'?id='.$id );
	return esc_url( get_site_url().'/'.$preview_post[0]->post_name.'/'.$id.'/' );
}



function woordf_get_version(){
	return '1.0.0'.date("Y_m_h_i_s");
}

//output buttons in order items
function woordf_admin_order_item_output_button($woordf_product_type,$woordf_product_vip){
	$vip=($woordf_product_vip==1)?'&vip=1':'';
	$href=admin_url( 'admin.php?page='.WORDF_ADMIN_NEW_PAGE_SLUG.'&order_id='.$_GET['post'].'&type='.$woordf_product_type.$vip);
	?><br /><a href="<?php echo $href;?>" class="button button-primary" style="margin:7px 7px 0 0"><?php _e('Attach files',WORDF_LANG);?></a><?php

}

//output row content
function woordf_admin_output_row_content($field){
	switch ($field) {
	    case 'order_id':
	        ?>
	        <label for="order"><?php _e('Choose order to attach files',WORDF_LANG);?></label>
	        <div class="woordf-droplist woordf-selector" data-target="<?php echo $field;?>">
	        	<?php woordf_output_orders_options();?>
	        </div>
	        <?php
	        break;
	    case 'type':
	    	?>
	        <label for="type"><?php _e('Choose upload type',WORDF_LANG);?></label>
	        <div class="woordf-radio woordf-selector" data-target="<?php echo $field;?>">
	        	<?php woordf_output_type_options();?>
	        </div>
	        <?php
	        break;


	}
}
//output row headers info
function woordf_admin_output_row_heading_values($field){
	$cl=isset($_GET[$field])?' class="not-empty"':'';
	switch ($field) {
	    case 'order_id':
	    	
		    if(isset($_GET[$field])){
				$order=wc_get_order($_GET[$field]);
				$info='#'.$order->ID.' '.$order->get_billing_first_name().' ('.woordf_get_datetime($order->get_date_created()).')';
			}
	    		
	        echo '<span'.$cl.'>'.((isset($_GET[$field]))?$info :'').'</span>';
	        break;
	    case 'type':
	    	echo '<span'.$cl.'>'.((isset($_GET[$field]))?woordf_get_type_label($_GET[$field]):'').'</span>';
	        break;

	}
}

function woordf_output_preview_button($order_id,$admin=false){

	$href=woordf_order_downloads_url().'?oid='.$order_id;
	echo '<a href="'.$href.'" class="btn button btn-primary view_order_files" data-order="'.$order_id.'">'.__('View',WORDF_LANG).'</a>';
}
//order downloads link 
function woordf_order_downloads_url(){
	return wc_get_page_permalink( 'myaccount' ).'order-downloads/';
}
//current page url 
function woordf_current_page_url(){
	global $wp;
	$current_url = home_url(add_query_arg(array(), $wp->request));
	return $current_url;
}

//order files, grid item thumbnail
function woordf_grid_item_thumbnail_attrs($record,$file=''){
	$class="image";
	$types=woordf_get_types();
	$type=$record['type'];
	$class.=' '.$types[$type]['action'];
	$style='';
	if($type==1 && $file!='') $thumbnail='';
	else if($file!='') $thumbnail=$file;
	else $thumbnail=woord_directory_handler::get_thumbnail($record['folder_name']);
	$class.=($thumbnail=='')?' no-thumb':'';
	if($thumbnail!=''){
		$style=" style=\"background-image:url('".$thumbnail."');\"";
	}
	
	echo 'class="'.$class.'"'.$style;
}
function woordf_admin_item_thumbnail_attrs($record,$file=''){
	$class="it-inner";
	$types=woordf_get_types();
	$type=$record['type'];
	$is_archyve=($file=='')?false : (woordf_get_file_extension($file)=='zip');
	$class.=($is_archyve)?' archyve' : ' '.$types[$type]['action'];
	$attrs='';
	if($type==1 && $file!=''){
		 $thumbnail='';
	}
	else $thumbnail=$file;

	$class.=($thumbnail=='' || $is_archyve)?' no-thumb':'';
	if($thumbnail!='' && !$is_archyve){
		$attrs=" style=\"background-image:url('".$thumbnail."');\" title=\"".$file."\"";
	}
	$attrs.=' data-file="'.woordf_filename_only($file).'"';

	
	echo 'class="'.$class.'"'.$attrs;
}
//preview button attrs
function woordf_grid_item_preview_button_attrs($record){
	$is_remote=$record['remote'];
	$attr_data=maybe_unserialize($record['attributes']);
	$preview_url=(isset($attr_data['preview']))?$attr_data['preview']:'#';
	$href=($is_remote)?$preview_url:'#';
	$class='btn button btn-primary';
	$class.=(!$is_remote)?' view_record_files':'';
	$attrs=(!$is_remote)?' data-id="'.$record['id'].'"':' target="_blank"';
	return 'href="'.$href.'" class="'.$class.'"'.$attrs;
}
//download button attrs
function woordf_output_download_button($record){
	$is_remote=$record['remote'];
	if(!$is_remote){
		$zip_file=woord_directory_handler::get_folder_content_list($record['folder_name'],'zip');
		$href=(!empty($zip_file))?$zip_file[0]:'#';
		$class='btn button btn-primary';
		$class.=(empty($zip_file))?' archive_files':'';
		$attrs=(empty($zip_file))?' data-fname="'.$record['folder_name'].'" folder="'.$record['folder_name'].'" target="_blank" download':' target="_blank" download';

		echo '<a href="'.$href.'" class="'.$class.'"'.$attrs.'>';
		woordf_output_icon('download');
		echo __('Download files',WORDF_LANG).'</a>';
	}
}
//rate button 
function woordf_output_rate_button($record){
	$is_vip=$record['vip'];
	//if($is_vip && !$record['confirmed'] && $admin_made_changes){
	
	if($is_vip){

		echo '<a href="#" class="rate-button btn button btn-primary" data-id="'.$record['id'].'">';
		woordf_output_icon('thumb-up');
		echo __('Rate',WORDF_LANG).'</a>';
	}
}

//embed button 

function woordf_output_embed_button($record){

	$is_remote=$record['remote'];
	$type=$record['type'];
	
	if(!$is_remote){

		echo '<a href="#" class="embed-button btn button btn-primary">';
		woordf_output_icon('split-v-alt');
		echo __('Embed',WORDF_LANG).'</a>';
	}
}

//embed content

function woordf_output_embed_content($record){
	$url=woordf_get_file_embed_url($record['folder_name']);
	$is_remote=$record['remote'];
	$type=$record['type'];
	
	if(!$is_remote){
?>
<p class="title"><?php _e('Embed gallery',WORDF_LANG)?></p>
<textarea id="embed_code"><iframe src="<?php echo $url;?>" frameBorder="0" style="width:100%;height:100%;min-height:500px;border:none;"></iframe></textarea>
<div class="response-text" style="display:none"><?php _e('Embed code was successfully copyed to your clipboard!',WORDF_LANG);?></div>
<?php
		echo '<a href="#" class="copy-embed btn button btn-primary" data-target="#embed_code">';
		woordf_output_icon('clipboard');
		echo __('Copy embed code',WORDF_LANG).'</a>';
	}
}
//get type slug
function woordf_get_type_slug($type){
	$types=woordf_get_types();
	return $types[$type]['action'];
}

//check files 

function woordf_remove_invalid_images($files){
	$result=array();
	foreach($files as $file){
		list($width, $height, $type, $attr) = getimagesize($file);
		if($width && $height) $result[]=$file;	
	}
	return $result;
}

//check file 

function woordf_valid_image($file){

	list($width, $height, $type, $attr) = getimagesize($file);
	if($width && $height) return $file;	
	else return false;
	

}
//info for js
function woordf_output_file_info_for_js($files){
	foreach($files as $key=>$file){
		list($width, $height, $type, $attr) = getimagesize($file);
		if($key>0) echo ',';
		echo '{src:"'.$file.'", w:'.$width.',h:'.$height.'}';
	} 
}

//last parrt of file path 
function woordf_filename_only($file){
	$fname_parts=explode("/",$file);
	return $fname_parts[count($fname_parts)-1];
}

//file extension 

function woordf_get_file_extension($file){
	$fname_parts=explode(".",$file);
	return $fname_parts[count($fname_parts)-1];
}
//output logs
function woordf_output_product_logs($logs,$admin=true){
	$table_classes=($admin)?'wp-list-table widefat fixed striped table-view-list':'wp-list-table logs-table';
	$hidden_style=(empty($logs))?' style="display:none"':'';
	$hidden_class=(empty($logs))?' hidden':'';
?>
<div class="log-table<?=$hidden_class ?>"<?=$hidden_style ?>>
<p class="lg"><?php _e('Product rating logs',WORDF_LANG);?></p>
<table class="<?php echo $table_classes; ?>">
	<thead>
		<tr>
			<?php
			$logs_table_columns=woordf_logs_table_columns();
			foreach($logs_table_columns as $column){
				?><th class="<?php echo $column['field']?>"><?php echo $column['label'];?></th><?php
			}
			?>
		</tr>
	</thead>
	<tbody>
	<?php
	if(!empty($logs)){
		foreach($logs as $log){
			?><tr><?php
			foreach($logs_table_columns as $column){
				?><td data-label="<?php echo $column['label'];?>" class="<?php echo $column['field'];?>"><span><?php echo woordf_get_label_by_log_field($column['field'],$log);?></span></td><?php
			}
			?></tr><?php
		}
	}
	?>
	
	</tbody>
</table>
</div>
<?php
}
//output admin logs
function woordf_output_admin_logs(){
	$table_classes='wp-list-table widefat fixed striped table-view-list admin-logs';
	$logs_db_actions=new woordf_logs_module();
	$logs=$logs_db_actions->get_admin_logs();
	if(!empty($logs)){
	?>

	<table class="<?php echo $table_classes; ?>">
		<thead>
			<tr>
				<?php
				$logs_table_columns=woordf_logs_table_columns(true);
				foreach($logs_table_columns as $column){
					?><th class="<?php echo $column['field']?>"><?php echo $column['label'];?></th><?php
				}
				?>
			</tr>
		</thead>
		<tbody>
		<?php
		foreach($logs as $log){
			?><tr><?php
			foreach($logs_table_columns as $column){
				?><td class="<?php echo $column['field'];?>"><span><?php echo woordf_get_label_by_log_field($column['field'],$log);?></span></td><?php
			}
			?></tr><?php
		}
		?>
		
		</tbody>
	</table>
	<?php
	}
	else _e('No logs yet.',WORDF_LANG);

}
//output one log row
function woordf_get_log_row($log){
	$logs_table_columns=woordf_logs_table_columns();
	ob_start();
	?><tr><?php
	foreach($logs_table_columns as $column){
		?><td data-label="<?php echo $column['label'];?>" class="<?php echo $column['field'];?>"><span><?php echo woordf_get_label_by_log_field($column['field'],$log);?></span></td><?php
	}
	?></tr><?php
	$output = ob_get_contents();
	ob_end_clean();
	return $output;
}
function woordf_get_max_rate_count(){
	$user_rate_count=get_option('user_rate_count');
	$user_rate_count=($user_rate_count!='')?$user_rate_count : 3;
	return $user_rate_count;
}

//how mutch rating user used

function woordf_get_rate_count_used($record_id){
	$max_count=woordf_get_max_rate_count();
	$db_logs_actions=new woordf_logs_module();
	$ratings_count=$db_logs_actions->get_user_rating_count($record_id);
	return $ratings_count.' / '.$max_count;
}

function woordf_max_count_used($record_id){
	$max_count=woordf_get_max_rate_count();
	$db_logs_actions=new woordf_logs_module();
	$ratings_count=$db_logs_actions->get_user_rating_count($record_id);
	return ($ratings_count==$max_count);
}
//360 presentation options
function woordf_output_presentation_options($attrs=''){
	$attributes=($attrs!='')?maybe_unserialize($attrs):'';
	//echo var_export($attributes,true);
	$field_data=(isset($attributes['fields']))?$attributes['fields'] : array();
	$presentation_options=woordf_get_presentation_options();
	?><p class="lg"><?php _e('Configure 360 / 3D photos parameters parameters',WORDF_LANG);?></p><?php
	foreach($presentation_options as $option){
		$fild_data=(isset($field_data[$option['key']]))?$field_data[$option['key']] : '';
		woordf_output_presentation_option_by_type($option,$fild_data);
	}

	?><a href="#" class="button button-primary update_options"><?php woordf_output_icon('save');_e('Update options',WORDF_LANG);?></a><?php
}

//output_presentation_option_by_type

function woordf_output_presentation_option_by_type($option,$val=''){

	$type=$option['type'];
	$default_value=(isset($option['default']))?$option['default']:'';
	$value=($val!='')?$val:$default_value;
	$key=$option['key'];
	$class=' '.$type.((isset($option['class']))?' '.$option['class']:'');

	?><div class="item<?= $class;?>"><?php
	$has_attr=(isset($option['attribute']) && $option['attribute']!='');
	$attrs=(isset($option['attribute_value_target']) && $option['attribute_value_target']!='')?' data-target="#'.$option['attribute_value_target'].'"':'';
	$attrs.=$has_attr ? ' data-attribute="'.$option['attribute'].'"':'';
	$attrs.=(isset($option['static_value'])) ? ' data-static_value="'.$option['static_value'].'"':'';
	$attrs.=(isset($option['element_show'])) ? ' data-element_show="'.$option['element_show'].'"':'';
	switch ($type) {
	    case 'number':
	    case 'text':

	    	?><label for="<?= $key;?>"><?=$option['label'] ?></label><?php
	        ?><input type="<?= $type;?>" name="<?= $key;?>" id="<?= $key;?>" value="<?= $value;?>" class="attr-input number_text"<?php echo $attrs;?> /><?php
	        break;
	    case 'checkbox':
	    	$checked=($value)?' checked':'';
	    	$value=1;

	    	?><label for="<?= $key;?>"><?=$option['label'] ?></label><?php
	        ?><input<?=$checked ?> class="attr-input <?=$type;?>" type="<?= $type;?>" name="<?= $key;?>" id="<?= $key;?>" value="<?= $value;?>"<?php echo $attrs;?> /><?php
	        break;
	    case 'select':
	    	if(isset($option['options']) && !empty($option['options'])):

		    	$attrs.=(isset($option['if_checked']) && $option['if_checked']!='') ? ' data-ifchecked="#'.$option['if_checked'].'"':'';
	       ?>
	       <label for="<?= $key;?>"><?=$option['label'] ?></label>
	       <select class="attr-input <?=$type;?>" name="<?= $key;?>" id="<?= $key;?>"<?php echo $attrs;?>>
	       <?php 
	       if(isset($option['options_labels']) && $option['options_labels']){
	       		foreach($option['options'] as $option_val=>$option_label){
					?><option value="<?= $option_val;?>"<?php echo ($value==$option_val)?' selected':'';?>><?=$option_label;?></option><?php
				}
		   }
		   else{
	       		foreach($option['options'] as $option_val){
					?><option value="<?= $option_val;?>"<?php echo ($value==$option_val)?' selected':'';?>><?=$option_val;?></option><?php
				}
		   }

	       ?>
	       </select>
	       <?php
	        endif;
	        break;
		default:
			echo '';
	}
	?></div><?php
}

//aditional attributes

function woordf_add_custom_attributes($attrs,$db_attrs){
	$attributes=$attrs;
	$saved_attributes=($db_attrs!='')?maybe_unserialize($db_attrs):'';
	$field_data=(isset($saved_attributes['attrs']))?$saved_attributes['attrs'] : array();
	if(!empty($field_data)){
		foreach($field_data as $key=>$val) $attributes[]=$key.'="'.$val.'"';
	}
	return $attributes;
}
//add default 
function woordf_add_default_attributes($attrs){
	$attributes=$attrs;
	$default_attributes=woordf_get_default_attributes(false);
	$field_data=(isset($default_attributes['attrs']))?$default_attributes['attrs'] : array();
	if(!empty($field_data)){
		foreach($field_data as $key=>$val) $attributes[]=$key.'="'.$val.'"';
	}
	return $attributes;
}
//primary attrs
function woordf_get_primary_atributes($files){
	$files_handler=new woordf_files_handler($files);
	$y_axis_enabled=$files_handler->y_axis_exist($files);
	$filename=$files_handler->get_filename('x');
	$files_count=$files_handler->get_files_count('x');
	$attributes=array();
	$attributes[]='data-filename-x="'.$filename.'"';
	$attributes[]='data-amount-x="'.$files_count.'"';
	//y axis
	if($y_axis_enabled){
		$filename=$files_handler->get_filename('y');
		$files_count=$files_handler->get_files_count('y');
		$attributes[]='data-filename-y="'.$filename.'"';//nike-y-{index}.jpg
		$attributes[]='data-amount-y="'.$files_count.'"';
	}
	
	
	return $attributes;
}

//show download button if remote
function woordf_show_remote_download_button($record){
	$is_remote=$record['remote'];
	if(!$is_remote) return false;
	$attr_data=maybe_unserialize($record['attributes']);
	$download_url=(isset($attr_data['download']) && $attr_data['download']!='')?$attr_data['download']:'';
	return ($download_url!='');
}

//download button if remote
function woordf_remote_download_button($record){
	$show=woordf_show_remote_download_button($record);
	if($show){
		$attr_data=maybe_unserialize($record['attributes']);
		$download_url=(isset($attr_data['download']) && $attr_data['download']!='')?$attr_data['download']:'';
		if ($download_url!=''){
			?><a href="<?php echo $download_url;?>" target="_blank" class="btn button btn-primary" download><?php _e('Download files',WORDF_LANG);?></a><?php
		}
	}

}

function woordf_send_from_email(){
	$from_mail = site_url();
	$from_mail = preg_replace('#^https?://#i', '', $from_mail);
	$from_mail = preg_replace('#^http?://#i', '', $from_mail);
	$pos = strrpos($from_mail, "/");
	if($pos!==false){
		$parts=explode("/",$from_mail);
		$from_mail=$parts[0];
	}
	
	$from_mail=get_bloginfo( 'name' ).'<info@'.$from_mail.'>';
	return $from_mail;
}
//send mail 

function woordf_send_mail($type='admin',$event='expired',$data){

	$admin_email=get_option(WORDF_PX.'admin_email');
	$admin_email=($admin_email=='')?get_option('admin_email'):$admin_email;
	$from_mail=woordf_send_from_email();
	$email_customer_days_before_content = get_option(WORDF_PX.'email_customer_days_before_content');
	$email_admin_days_before_content = get_option(WORDF_PX.'email_admin_days_before_content');
	$email_customer_after_remove_content = get_option(WORDF_PX.'email_customer_after_remove_content');
	$email_admin_after_remove_content = get_option(WORDF_PX.'email_admin_after_remove_content');
	$log_report_days_before = get_option(WORDF_PX.'log_report_days_before');
	$subject = "Nauja žinutė";
	$today=woordf_get_datetime('','Y-m-d');
	if(isset($data['expires'])) $expires=woordf_get_datetime($data['expires'],'Y-m-d');
	if(isset($expires)) $days_difference=woordf_get_days_difference($today,$expires);
	else $days_difference=$log_report_days_before;
	if($type=='admin'){
		$to=($admin_email && $admin_email!='')?$admin_email:'';
		if($event=='expired'){
			$subject=__('Administrator, record {record} expired and was removed.', WORDF_LANG);
			$content=$email_admin_after_remove_content;
		}
		else if($event=='customer_rated'){
			$subject=__('Admin, product {record} has been rated by a user {customer}', WORDF_LANG);
			$content=get_option(WORDF_PX.'email_admin_after_customer_rates_content');
			
		}
		else{
			$subject=__('Administrator, record {record} will expire after {days} days.', WORDF_LANG);
			$content=$email_admin_days_before_content;
			
		}
	}
	else if($type=='customer'){
		if(isset($data['user_id'])){
			$user_info = get_userdata((int)$data['user_id']);
			$to=$user_info->user_email;
			$to=(filter_var($to, FILTER_VALIDATE_EMAIL))?$to:'';
		}

		if($event==='expired'){
			$subject=__('Record {record} expired and was removed.', WORDF_LANG);
			$content=$email_customer_after_remove_content;
		}
		else if($event=='admin_rated'){
			$subject=__('Dear customer, we would like to inform you, that the administrator has made changes to the product {record} and commented', WORDF_LANG);
			$content=get_option(WORDF_PX.'email_customer_after_admin_rates_content');
			
		}
		else{
			$subject=__('Record {record} will expire after {days} days.', WORDF_LANG);
			$content=$email_customer_days_before_content;
		}
	}


	
	if(isset($content) && $content!='' && isset($to) && $to!=''){
		if($event=='customer_rated' || $event=='admin_rated'){
			$keywords = array("{customer}", "{record}", "{order_id}","{comment}", "{br}");
			$replacement   = array($data['customer'], $data['record'],$data['order_id'],$data['comment'],"<br>");
		}
		else{
			$keywords = array("{days}", "{record}", "{order_id}", "{br}");
			$replacement   = array($days_difference, '#'.$data['record_id'].$data['label'],$data['order_id'],"<br>");
		}

		$subject = str_replace($keywords, $replacement, $subject);
		$content = str_replace($keywords, $replacement, $content);
		$headers = array('Content-Type: text/html; charset=UTF-8','From: '.$from_mail);
		$body=$content.'<br><hr />'.__('Letter was generated automatically by ', WORDF_LANG).get_bloginfo( 'name' );
		
		wp_mail( $to, $subject, $body, $headers );

	}


}

//categories 

function woordf_categories_select($cat_id=0,$show_empty_option=false){
	$db_actions=new woordf_categories_module();
	if($cat_id==0){
		$cat_id=(isset($_GET['category']) && $_GET['category']!='')? (int)$_GET['category']:$cat_id;
	}
	
	$results=$db_actions->get_table_data(array('id'=>'ASC'));
	?><select id="category" class="input" data-attr="category" name="category" autocomplete="off"><?php
		if($show_empty_option){
			?><option value=""><?php _e('Choose category',WORDF_LANG)?></option><?php
		}
		if(!empty($results)){
			woordf_categories_select_options($results,$cat_id);
		}
	?></select><?php
}
function woordf_categories_select_options($results=array(),$cat_id=0){
	if(empty($results)){
		$db_actions=new woordf_categories_module();
		$results=$db_actions->get_table_data(array('id'=>'ASC'));
	}
	if(!empty($results)){
		foreach($results as $category){
			?><option<?php echo ($cat_id==$category['id'])?' selected':'';?> value="<?php echo $category['id'];?>"><?php echo $category['name'];?></option><?php
		}
	}

}
//create new 
function woordf_create_new_category_element(){
	?><br /><a href="#" class="create-new-category"><?php _e('Create new category',WORDF_LANG)?></a><?php
	?><div id="new_cat_wrapper" class="add-category" style="display:none">
		<input type="text" id="new_category" placeholder="<?php _e('New category name',WORDF_LANG)?>" autocomplete="off" /><div id="add_category" class="btn"><?php woordf_output_icon('plus');echo '<span>'.__('Add',WORDF_LANG).'</span>'; ?></div>
	</div><?php
}

//styled checkmark 

function woordf_output_styled_success_checkmark($new=false){
	?>
	<div class="styled_success_mark">
		<div class="inner">
			<div class="element">
				<svg version="1.1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 130.2 130.2">
				  <circle class="path circle" fill="none" stroke="#73AF55" stroke-width="6" stroke-miterlimit="10" cx="65.1" cy="65.1" r="62.1"/>
				  <polyline class="path check" fill="none" stroke="#73AF55" stroke-width="6" stroke-linecap="round" stroke-miterlimit="10" points="100.2,40.2 51.5,88.8 29.8,67.5 "/>
				</svg>
				<p class="success success_message"><?php _e('Record was updated successfully!',WORDF_LANG);?></p>
				<?php if($new):?>
					<span class="title"><?php _e('List of files',WORDF_LANG);?>:</span>
					<div id="files_output"></div>
					<div class="centered"><a href="<?php echo admin_url( 'admin.php?page='.WORDF_ADMIN_PAGE_SLUG);?>" class="button button-primary woordf-go-edit" style="display:none;"><?php woordf_output_icon('edit');_e('Edit last record',WORDF_LANG);?></a></div>
				<?php endif;?>
			</div>
		</div>

	</div>
	<?php
}
function woordf_current_user_role($role='administrator',$user_id=0) {
 
  if( is_user_logged_in() ) {
		if($user_id==0){
			$user = wp_get_current_user();
		}
		else{
			$user =new WP_User($user_id);
		}
   
		$roles = ( array ) $user->roles;
 
		return in_array($role,$roles); 
 
	} else {
 
		return false;
 
	}
 
}
?>