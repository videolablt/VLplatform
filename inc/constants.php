<?php
function woordf_admin_options_fields(){
	$options=array();
	
	//group - main options
	
	$options[]=array(
		'name'=>'main',
		'label'=>__('Main options', WORDF_LANG),
		'type'=>'group'
		
    );
    //--fields
	$options[]=array(
		'name'=>WORDF_PX.'user_rate_count',
		'label'=>__('How many times user can rate Premium product', WORDF_LANG),
		'type'=>'number',
		'default'=>3,
		'group'=>'main'
		
    );
	$options[]=array(
		'name'=>WORDF_PX.'records_per_page',
		'label'=>__('How many records show per page', WORDF_LANG),
		'type'=>'number',
		'default'=>20,
		'group'=>'main'
    );
	$options[]=array(
		'name'=>WORDF_PX.'categories_per_page',
		'label'=>__('How many categories show per page', WORDF_LANG),
		'type'=>'number',
		'default'=>20,
		'group'=>'main'
    ); 
    //group -- Logs and e-mail automatic notifiacations
    
	$options[]=array(
		'name'=>'notifiacations',
		'label'=>__('Logs and e-mail automatic notifiacations', WORDF_LANG),
		'type'=>'group'
		
    );
    
    //--fields
	$options[]=array(
		'name'=>WORDF_PX.'admin_email',
		'label'=>__('E-mail address to which letters will be sent to the administrator', WORDF_LANG),
		'type'=>'text',
		'default'=>get_option('admin_email'),
		'group'=>'notifiacations'
		
    );
	$options[]=array(
		'name'=>WORDF_PX.'log_report_days_before',
		'label'=>__('Specify the number of days until expiration (will be used for logs and emails)', WORDF_LANG),
		'type'=>'number',
		'default'=>3,
		'group'=>'notifiacations'
		
    );

	$options[]=array(
		'name'=>WORDF_PX.'email_customer_days_before_enabled',
		'label'=>__('Send email to customer', WORDF_LANG).', '.__('when the specified number of days remain before the expiration date', WORDF_LANG),
		'type'=>'checkbox',
		'default'=>1,
		'group'=>'notifiacations'
    ); 
	$options[]=array(
		'name'=>WORDF_PX.'email_admin_days_before_enabled',
		'label'=>__('Send email to administrator', WORDF_LANG).', '.__('when the specified number of days remain before the expiration date', WORDF_LANG),
		'type'=>'checkbox',
		'default'=>1,
		'group'=>'notifiacations'
		
    );
	$options[]=array(
		'name'=>WORDF_PX.'email_customer_after_remove_enabled',
		'label'=>__('Send email to customer', WORDF_LANG).', '.__('when files storing deadline expires', WORDF_LANG),
		'type'=>'checkbox',
		'default'=>1,
		'group'=>'notifiacations'
		
    ); 
	$options[]=array(
		'name'=>WORDF_PX.'email_admin_after_remove_enabled',
		'label'=>__('Send email to administrator', WORDF_LANG).', '.__('when files storing deadline expires', WORDF_LANG),
		'type'=>'checkbox',
		'default'=>1,
		'group'=>'notifiacations'	
    );
	$options[]=array(
		'name'=>WORDF_PX.'email_admin_after_customer_rates',
		'label'=>__('Send email to administrator', WORDF_LANG).', '.__('when customer rates product', WORDF_LANG),
		'type'=>'checkbox',
		'default'=>1,
		'group'=>'notifiacations'
    );
	$options[]=array(
		'name'=>WORDF_PX.'email_customer_after_admin_rates',
		'label'=>__('Send email to customer', WORDF_LANG).', '.__('when administrator rates product', WORDF_LANG),
		'type'=>'checkbox',
		'default'=>1,
		'group'=>'notifiacations'
    );
    //templates
	$options[]=array(
		'name'=>WORDF_PX.'email_customer_days_before_content',
		'label'=>__('The content of the email when the message is sent to the customer', WORDF_LANG).', '.__('when the specified number of days remain before the expiration date', WORDF_LANG),
		'type'=>'textarea',
		'default'=>__("We would like to inform you that your file storage will expire in {days} days, hurry up to download your files if you haven't already!",WORDF_LANG),
		'tips'=>sprintf(__('Keywords you can use in content: %s - %s, %s - %s, %s - %s, %s - %s', WORDF_LANG), '{days}',__('day count until expire date.', WORDF_LANG), '{record}',__('record information', WORDF_LANG), '{order_id}',__('order id', WORDF_LANG),'{br}',__('line break', WORDF_LANG)),
		'group'=>'notifiacations'
    );
	$options[]=array(
		'name'=>WORDF_PX.'email_admin_days_before_content',
		'label'=>__('The content of the email when the message is sent to the administrator', WORDF_LANG).', '.__('when files storing deadline expires', WORDF_LANG),
		'type'=>'textarea',
		'default'=>__("We would like to inform you that in {days} days the storage term for the record {record} files will expire!",WORDF_LANG),
		'tips'=>sprintf(__('Keywords you can use in content: %s - %s, %s - %s, %s - %s, %s - %s', WORDF_LANG), '{days}',__('day count until expire date.', WORDF_LANG), '{record}',__('record information', WORDF_LANG), '{order_id}',__('order id', WORDF_LANG),'{br}',__('line break', WORDF_LANG)),
		'group'=>'notifiacations'
    );
	$options[]=array(
		'name'=>WORDF_PX.'email_customer_after_remove_content',
		'label'=>__('The content of the email when the message is sent to the customer', WORDF_LANG).', '.__('when files storing deadline expires', WORDF_LANG),
		'type'=>'textarea',
		'default'=>__("We would like to inform you that your files on the server have expired and have been removed!",WORDF_LANG),
		'tips'=>sprintf(__('Keywords you can use in content: %s - %s, %s - %s, %s - %s, %s - %s', WORDF_LANG), '{days}',__('day count until expire date.', WORDF_LANG), '{record}',__('record information', WORDF_LANG), '{order_id}',__('order id', WORDF_LANG),'{br}',__('line break', WORDF_LANG)),
		'group'=>'notifiacations'
    );
	$options[]=array(
		'name'=>WORDF_PX.'email_admin_after_remove_content',
		'label'=>__('The content of the email when the message is sent to the administrator', WORDF_LANG).', '.__('when files storing deadline expires', WORDF_LANG),
		'type'=>'textarea',
		'default'=>__("We would like to inform you that record {record} files on the server have expired and have been removed!",WORDF_LANG),
		'tips'=>sprintf(__('Keywords you can use in content: %s - %s, %s - %s, %s - %s, %s - %s', WORDF_LANG), '{days}',__('day count until expire date.', WORDF_LANG), '{record}',__('record information', WORDF_LANG), '{order_id}',__('order id', WORDF_LANG),'{br}',__('line break', WORDF_LANG)),
		'group'=>'notifiacations'
    );
	$options[]=array(
		'name'=>WORDF_PX.'email_admin_after_customer_rates_content',
		'label'=>__('The content of the email when the message is sent to the administrator', WORDF_LANG).', '.__('when customer rates product', WORDF_LANG),
		'type'=>'textarea',
		'default'=>__("Customer - {customer} rated product {record}, {br}comment: {comment}. {br}Order id: {order_id}",WORDF_LANG),
		'tips'=>sprintf(__('Keywords you can use in content: %s - %s, %s - %s, %s - %s, %s - %s, %s - %s', WORDF_LANG), '{customer}',__('customer information.', WORDF_LANG), '{comment}',__('customer comment.', WORDF_LANG), '{record}',__('record information', WORDF_LANG), '{order_id}',__('order id', WORDF_LANG),'{br}',__('line break', WORDF_LANG)),
		'group'=>'notifiacations'
    );
	$options[]=array(
		'name'=>WORDF_PX.'email_customer_after_admin_rates_content',
		'label'=>__('The content of the email when the message is sent to the customer', WORDF_LANG).', '.__('when administrator rates product', WORDF_LANG),
		'type'=>'textarea',
		'default'=>__("Administrator commented product {record}, {br}comment: {comment}. {br}Order id: {order_id}. {br}Please take a look at updated product and leave your feedback.",WORDF_LANG),
		'tips'=>sprintf(__('Keywords you can use in content: %s - %s, %s - %s, %s - %s, %s - %s', WORDF_LANG), '{comment}',__('customer comment.', WORDF_LANG), '{record}',__('record information', WORDF_LANG), '{order_id}',__('order id', WORDF_LANG),'{br}',__('line break', WORDF_LANG)),
		'group'=>'notifiacations'
    );
	return $options;
}
//admin records labels
function woordAdmin_records_columns(){
	return array(
        'id'         => __('ID',WORDF_LANG),
        'label'=> __('Label',WORDF_LANG),
        'category'=> __('Category',WORDF_LANG),
        'shortcode'  => __('Shortcode',WORDF_LANG),
	    'expires'=> __('Expires',WORDF_LANG),
        'updated'       => __('Updated',WORDF_LANG),
        'created'       => __('Created',WORDF_LANG),
        'actions'       => __('Actions',WORDF_LANG)
        
    );
}
//admin categories columns
function woordAdmin_categories_columns(){
	return array(
        'id'         => __('ID',WORDF_LANG),
        'name'=> __('Category name',WORDF_LANG),
        'actions'       => __('Actions',WORDF_LANG)

        
    );
}
//admin order labels
function woordf_admin_record_tabel_labels(){
	return array(
        'id'         => __('Upload no.',WORDF_LANG),
        'label'=> __('Label',WORDF_LANG),
        'category'		=>__('Category',WORDF_LANG),
        'order'      =>  __('Order',WORDF_LANG),
        'user'		=>__('User',WORDF_LANG),
        'type' => __('Service',WORDF_LANG),
        'file_url'=>__('File url',WORDF_LANG),
        'folder_name'=>__('Folder name',WORDF_LANG),
	    'remote'=>__('Localization',WORDF_LANG),
	    'vip'=>__('Premium / Standart',WORDF_LANG),
	    'confirmed'=>__('Confirmed',WORDF_LANG),
	    'expires'=> __('Expires',WORDF_LANG),
        'updated'       => __('Updated',WORDF_LANG),
        'created'       => __('Created',WORDF_LANG),
        'actions'       => __('Actions',WORDF_LANG)
        
    );
}

//admin logs collumns
function woordf_admin_logs_columns(){
	return array(
        'id'         => __('Record',WORDF_LANG),
        'type'       => __('Comment author',WORDF_LANG),
        'user_id' 	 =>__('Name',WORDF_LANG),
        'created'    => __('Created',WORDF_LANG),
        'comment'    => __('Comment',WORDF_LANG),
       
        
    );
}
//order filters
function woordf_admin_order_filters(){
	$ind=0;
	return array(
			($ind++)=>array(
				'name'=>'type',
				'label'=>__('By service',WORDF_LANG),
				'data'=>woordf_get_types(true)
			),
			($ind++)=>array(
				'name'=>'user_id',
				'label'=>__('By user',WORDF_LANG),
				'data'=>woordf_get_table_users()
			),
			($ind++)=>array(
				'name'=>'order_id',
				'label'=>__('By order',WORDF_LANG),
				'data'=>woordf_get_table_orders()
			),
			($ind++)=>array(
				'name'=>'category',
				'label'=>__('By category',WORDF_LANG),
				'data'=>woordf_get_categories()
			),
		);
}
//admin input fields
function woordf_admin_input_fields(){
	$ind=0;
	return array(
			($ind++)=>array(
				'name'=>'order_id',
				'label'=>__('Order',WORDF_LANG)
			),
			($ind++)=>array(
				'name'=>'type',
				'label'=>__('Type',WORDF_LANG),
			)
		);
}
//types
function woordf_get_types($labels_only=false){

	$result=array();
	if($labels_only){
		$result[1]=__("Video",WORDF_LANG);
		$result[2]=__("Photos",WORDF_LANG);
		$result[3]=__("360 / 3D photo",WORDF_LANG);
	}
	else{
		$result[1]=array('label'=>__("Video",WORDF_LANG),'action'=>'video');
		$result[2]=array('label'=>__("Photos",WORDF_LANG),'action'=>'photos');
		$result[3]=array('label'=>__("360 / 3D photo",WORDF_LANG),'action'=>'image_360');
	}

	return $result;
}
//logs headers
function woordf_logs_table_columns(){

	$result=array();

	$result[]=array('label'=>__("Created",WORDF_LANG),'field'=>'created');
	$result[]=array('label'=>__("Comment author",WORDF_LANG),'field'=>'type');
	$result[]=array('label'=>__("Rate value",WORDF_LANG),'field'=>'value');
	$result[]=array('label'=>__("Comment",WORDF_LANG),'field'=>'comment');	
	
	return $result;
}
//log value by type 
function woordf_get_label_by_log_field($type,$values){
	$val=$values[$type];
	if($type=='type'){
		if($val=='report') return __('System',WORDF_LANG);
		else return ($val=='rate')?__('User',WORDF_LANG) : __('Administrator',WORDF_LANG);
	}
	else if($type=='value'){
		if($values['type']!='admin') return ($val==0)?woordf_output_icon('alert',__('Not',WORDF_LANG),false).'<span>'.__('Not confirmed',WORDF_LANG).'</span>' :woordf_output_icon('check-box',__('Confirmed',WORDF_LANG),false).'<span>'.__('Confirmed',WORDF_LANG).'</span>';
		else return woordf_output_icon('marker-alt',__('Updated',WORDF_LANG),false).'<span>'.__('Updated',WORDF_LANG).'</span>';
	}
	else return $val;

}

function woordf_icon_by_type_and_key($type,$key){
	if($type=='type'){
		$icons=array('','video-camera','gallery','loop');
		return (isset($icons[$key]))?$icons[$key]:'';
	}

}
//download
function woordf_get_label_by_val($type,$val){
	if($type=='vip'){
		return ((int)$val==0)?__('Standart',WORDF_LANG) : __('Premium',WORDF_LANG);
	}
	if($type=='confirmed'){
		return ((int)$val==0)?__('Not',WORDF_LANG) : __('Confirmed',WORDF_LANG);
	}
	if($type=='remote'){
		return ((int)$val==0)?__('Local files',WORDF_LANG) : __('Remote files',WORDF_LANG);
	}
}
//icon
function woordf_get_icon_by_val($type,$val){
	if($type=='vip'){
		return ((int)$val==0)?'-' :woordf_output_icon('star', __('Premium',WORDF_LANG),false);
	}
	if($type=='confirmed'){
		return ((int)$val==0)?woordf_output_icon('alert',__('Not',WORDF_LANG),false).__('Not',WORDF_LANG) : woordf_output_icon('check-box', __('Confirmed',WORDF_LANG),false).__('Confirmed',WORDF_LANG);
	}
	if($type=='remote'){
		return ((int)$val==0)?woordf_output_icon('clip',__('Local files',WORDF_LANG),false) :woordf_output_icon('cloud-up', __('Remote files',WORDF_LANG),false);
	}
}
//columns 
function woordf_get_account_downloads_columns(){
	return array(
		'order_id'=>__('Order ID',WORDF_LANG),
		'type'=>__('Products types',WORDF_LANG),
		'date'=>__('Date',WORDF_LANG),
		'actions'=>__('Actions',WORDF_LANG),
	);
}

//endpoints 
function woordf_woo_my_account_endpoints(){
	
	$items=array();
	//$items[]=array('label'=>'Files preview','endpoint'=>'files-preview','view'=>'preview','in_woo_navigation'=>false);
	$items[]=array('label'=>'Production','endpoint'=>'order-downloads','view'=>'order_downloads','in_woo_navigation'=>true);
	return $items;
}
//presentation_options

function woordf_get_presentation_options(){
	$options=array();
	$options[]=array(
		'type'=>'checkbox',
		'key'=>'pointer_zoom_enabled',
		'label'=>__('Pointer zoom enabled'),
		'attribute'=>'data-pointer-zoom',
		'attribute_value_target'=>'zoom_scale',
		'static_value'=>'true',
		'class'=>''
	);
	$options[]=array(
		'type'=>'select',
		'options'=>array('1.5','2','2.5','3','3.5','4'),
		'option_labels'=>false,
		'key'=>'zoom_scale',
		'label'=>__('Zoom scale'),
		'attribute'=>'',
		'if_checked'=>'pointer_zoom_enabled',
		'attribute_value_target'=>'',
		'class'=>'inv'
	);
	$options[]=array(
		'type'=>'number',
		'key'=>'drag_speed',
		'label'=>__('Drag speed(milliseconds)'),
		'attribute'=>'data-drag-speed',
		'attribute_value_target'=>'',
		'default'=>120,
		'class'=>''
	);
	$options[]=array(
		'type'=>'number',
		'key'=>'autoplay_speed',
		'label'=>__('Auto play speed(milliseconds)'),
		'attribute'=>'data-speed',
		'attribute_value_target'=>'',
		'default'=>120,
		'class'=>'inv autoplay_elements'
	);
	$options[]=array(
		'type'=>'checkbox',
		'key'=>'autoplay_enabled',
		'label'=>__('Autoplay','any'),
		'attribute'=>'data-autoplay',
		'attribute_value_target'=>'',
		'static_value'=>'true',
		'element_show'=>'.autoplay_elements',
		'class'=>''
	);
	$options[]=array(
		'type'=>'checkbox',
		'key'=>'autoplay_reversed',
		'label'=>__('Autoplay reversed'),
		'attribute'=>'data-autoplay-reverse',
		'attribute_value_target'=>'',
		'static_value'=>'true',
		'class'=>'inv autoplay_elements'
	);
	$options[]=array(
		'type'=>'checkbox',
		'key'=>'play_once',
		'label'=>__('Play once'),
		'attribute'=>'data-play-once',
		'attribute_value_target'=>'',
		'static_value'=>'true',
		'class'=>'inv autoplay_elements'
	);

	$options[]=array(
		'type'=>'checkbox',
		'key'=>'full_screen',
		'label'=>__('Full screen'),
		'attribute'=>'data-fullscreen',
		'attribute_value_target'=>'',
		'static_value'=>'true',
		'class'=>''
	);
	$options[]=array(
		'type'=>'checkbox',
		'key'=>'disable_drag',
		'label'=>__('Disable drag'),
		'attribute'=>'disable-drag',
		'attribute_value_target'=>'',
		'static_value'=>'true',
		'class'=>''
	);
	$options[]=array(
		'type'=>'checkbox',
		'key'=>'drag_reverse',
		'label'=>__('Drag reverse'),
		'attribute'=>'data-spin-reverse',
		'attribute_value_target'=>'',
		'static_value'=>'true',
		'class'=>''
	);
	$options[]=array(
		'type'=>'checkbox',
		'key'=>'hide_logo',
		'label'=>__('Hide 360 logo'),
		'attribute'=>'hide-360-logo',
		'attribute_value_target'=>'',
		'static_value'=>'true',
		'class'=>''
	);
	$options[]=array(
		'type'=>'checkbox',
		'key'=>'keys_controll',
		'label'=>__('Control by keyboard'),
		'attribute'=>'data-keys',
		'attribute_value_target'=>'',
		'static_value'=>'true',
		'class'=>''
	);
	/*
	$options[]=array(
		'type'=>'checkbox',
		'key'=>'keys_controll_reverse',
		'label'=>__('Control by keyboard reverse'),
		'attribute'=>'data-control-reverse',
		'attribute_value_target'=>'',
		'static_value'=>'true',
		'class'=>''
	);
	*/
	$options[]=array(
		'type'=>'checkbox',
		'key'=>'stop_at_edges',
		'label'=>__('Stop at edges'),
		'attribute'=>'stop-at-edges',
		'attribute_value_target'=>'',
		'static_value'=>'true',
		'class'=>''
	);
	$options[]=array(
		'type'=>'checkbox',
		'key'=>'bottom_circle',
		'label'=>__('Bottom circle'),
		'attribute'=>'data-bottom-circle',
		'static_value'=>'true',
		'attribute_value_target'=>'',
		'class'=>''
	);

	$options[]=array(
		'type'=>'checkbox',
		'key'=>'magnifyer',
		'label'=>__('Magnifyer'),
		'attribute'=>'data-magnifier',
		'attribute_value_target'=>'',
		'static_value'=>2,
		'class'=>''
	);

	return $options;
}

//default attributes
function woordf_get_default_attributes($serialized=true){
	$default_parameters=get_option('woordf_default_parameters');
	$default_parameters= (!$default_parameters || $default_parameters=='')?array('attrs'=>array('data-drag-speed'=>'120'),'fields'=>array('drag_speed'=>'120')):$default_parameters;
	if($serialized) return maybe_serialize($default_parameters);
	else return $default_parameters;
}
?>