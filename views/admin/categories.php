<div class="wrap woordf-wrapper categories">
	<h2><?php echo __('Categories',WORDF_LANG); ?></h2>
	<div class="after_update" style="display:none"><?php _e('Category was successfully added!',WORDF_LANG);_e(' (Page will be reloaded to view updates.)',WORDF_LANG);?></div>
	<p class="label"><?php _e('Add new category',WORDF_LANG);?></p>
	<div class="add-category">
		<input type="text" id="new_category" placeholder="<?php _e('New category name',WORDF_LANG)?>" autocomplete="off" /><div id="add_category" class="btn"><?php woordf_output_icon('plus');echo '<span>'.__('Add',WORDF_LANG).'</span>'; ?></div>
	</div>
	<div id="woordf_categories_form">
	    <form method="post">
	    <?php $table->display(); ?>
	    </form>
	</div> 

</div>
<script>

(function($){
	var current_message_id=0;

	function remove_after_message(){
		current_message_id++;
		var message_id=current_message_id;
    	setTimeout(function(){
    		if(message_id==current_message_id){
	    		$('.after_update').fadeOut(300,function(){
	    			if($('.after_update').hasClass('woordf-success')) $('.after_update').removeClass('woordf-success');
	    			if($('.after_update').hasClass('woordf-error')) $('.after_update').removeClass('woordf-error');
	    			$('.after_update').removeClass('woordf-message');
	    		});	
			}
    	},4000);
	}

	$(document).on('click tap', ".edit_category", function(event) {
		event.preventDefault();
		var th=$(this);
		var el=th.closest('tr').find('.inner');
		if(!el.hasClass('editable')) el.addClass('editable');


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
				return_table:1,
			};


	        $.ajax({
	            type: 'POST',
	            url: "<?php echo admin_url('admin-ajax.php')?>",
	            data: attr_data,
	            success: function(resp) {
	            	data = JSON.parse(resp);
	            	//console.log(data);
	            	parent.removeClass('loading');
	            	input_field.val('');
	            	var fclass=(data.status==1)?'success':'error';
	            	$('.after_update').html(data.message).addClass('woordf-message woordf-'+fclass).fadeIn(300);
	            	if(data.status==1) $('#woordf_categories_form').html(data.table);
	            	remove_after_message();

	            },
	            error: function(err) {
	                console.log(err);

	            }
	        });

		}
		else input_field.addClass('error');

    });
	//update category
	
	$(document).on('click tap', ".update", function(event) {
		event.preventDefault();
		var th=$(this);
		var parent=$(".woordf-wrapper");
		var input_field=th.parent().find('input');
		var val=input_field.val();
		var el=th.closest('tr').find('.inner');
		if(val!=''){
			if(input_field.hasClass('error')) input_field.removeClass('error');
			parent.addClass('loading');
			attr_data={
				action:'woordf_update_category',
				id: th.attr('data-id'),
				name: val,
			}

	        $.ajax({
	            type: 'POST',
	            url: "<?php echo admin_url('admin-ajax.php')?>",
	            data: attr_data,
	            success: function(resp) {
	            	data = JSON.parse(resp);
	            	//console.log(data);
	            	parent.removeClass('loading');
	            	if(data.status==1){
						el.find('span.name').html(val);
						if(el.hasClass('editable')) el.removeClass('editable');
					}
	            	var fclass=(data.status==1)?'success':'error';
	            	$('.after_update').html(data.message).addClass('woordf-message woordf-'+fclass).fadeIn(300);
					remove_after_message();
	            },
	            error: function(err) {
	                console.log(err);

	            }
	        });
		}
		else input_field.addClass('error')

    });
	//remove category
	
	$(document).on('click tap', ".remove_category", function(event) {
		event.preventDefault();
		var th=$(this);
		var cat_id=th.closest('tr').find('input[type=checkbox]').val();
		var el_row=th.closest('tr');
		if(cat_id!=''){
			el_row.addClass('removing');

			attr_data={
				action:'woordf_remove_category',
				id: cat_id,
			}

	        $.ajax({
	            type: 'POST',
	            url: "<?php echo admin_url('admin-ajax.php')?>",
	            data: attr_data,
	            success: function(resp) {
	            	data = JSON.parse(resp);
	            	//console.log(data);

	            	if(data.status==1){
						el_row.fadeOut(500);//.delay(200)

					}
	            	var fclass=(data.status==1)?'success':'error';
	            	$('.after_update').html(data.message).addClass('woordf-message woordf-'+fclass).fadeIn(300);
	            	remove_after_message();
	            },
	            error: function(err) {
	                console.log(err);

	            }
	        });
		}
		else input_field.addClass('error')

    });
	

	
	$(document).ready(function()
	{

	});
	
})(jQuery);	
</script>