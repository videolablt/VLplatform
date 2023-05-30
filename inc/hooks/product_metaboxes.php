<?php

add_action( 'add_meta_boxes', 'woordf_metaboxes' );
function woordf_metaboxes()
{
    add_meta_box( 'woordf_metabox_id1', __( 'Order files plugin field',WORDF_LANG), 'woordf_product_metabox_output', 'product', 'side', 'high' );

}
function woordf_product_metabox_output($post)
{
	$values = get_post_custom( $post->ID );
	$woordf_product_type = isset( $values['woordf_product_type'] ) ? $values['woordf_product_type'][0] : 0;
	$woordf_product_vip = isset( $values['woordf_product_vip'] ) ? $values['woordf_product_vip'][0] : 0;
	wp_nonce_field( 'woordf_product_metabox_nonce', 'meta_box_nonce' );
	
	$vip_selected=($woordf_product_vip==1)?' selected':'';
	?>
	<div class="wrap woordf-product-meta">
		<div class="clearfix type">	
			<input type="hidden" name="woordf_product_type" value="<?php echo $woordf_product_type;?>">
	        <label for="type"><?php _e('Choose files type',WORDF_LANG);?></label>
	        <div class="woordf-droplist woordf-selector" data-target="woordf_product_type">
	        	<?php woordf_output_type_options();?>
	        </div>
		</div>	
		<div class="clearfix vip">	
			<input type="hidden" name="woordf_product_vip" value="<?php echo $woordf_product_vip;?>">
	        <label for="type"><?php _e('Is this Premium?',WORDF_LANG);?></label>
	        <div class="woordf-droplist woordf-selector" data-target="woordf_product_vip">
				<div class="option" data-value="1">
						<div class="check"><?php woordf_output_icon('check');?></div>
						<div class="text"><div class="inner"><?php woordf_output_icon('star');?><?php _e('Premium product',WORDF_LANG)?></div></div>
				</div>
	        </div>
		</div>	
	</div>
<script>
(function($){
	function woordf_check_options(){
		var val=$('[name=woordf_product_type]').val();
		if(val!=''){
			$( '.woordf-product-meta .type .woordf-selector .option' ).each(function() {
				var this_val=$(this).attr('data-value');
				if(this_val==val) $(this).trigger('click');
			});
		}
		var val2=$('[name=woordf_product_vip]').val();

		if(val2!=''){
			$( '.woordf-product-meta .vip .woordf-selector .option' ).each(function() {
				var this_val=$(this).attr('data-value');
				if(this_val==val2) $(this).trigger('click');
			});
		}


	}
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
		else $('[name='+target_name+']').val("0").trigger('change');


    });
    
	$(document).ready(function()
	{
		woordf_check_options();

	
	});
})(jQuery);	
</script>
	<?php
}
//save
add_action( 'save_post', 'woordf_product_metabox_save' );
function woordf_product_metabox_save( $post_id )
{
    if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
   
    if( !isset( $_POST['meta_box_nonce'] ) || !wp_verify_nonce( $_POST['meta_box_nonce'], 'woordf_product_metabox_nonce' ) ) return;
     
    $user = wp_get_current_user();

    if( !$user->has_cap( 'edit_posts' ) ) return;
         
        
    if( isset( $_POST['woordf_product_type'])) update_post_meta( $post_id, 'woordf_product_type', esc_attr( $_POST['woordf_product_type'] ) );
    else update_post_meta( $post_id, 'woordf_product_type', 0 );
    if( isset( $_POST['woordf_product_vip'])) update_post_meta( $post_id, 'woordf_product_vip', esc_attr( $_POST['woordf_product_vip'] ) );
    else update_post_meta( $post_id, 'woordf_product_vip', 0 );
        
      
}


?>