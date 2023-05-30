<div class="woordf-wrapper">
	<h1><?php _e('Order uploads',WORDF_LANG);?></h1>
	<form action="<?php echo admin_url( 'admin.php?'); ?>" method="GET" class="woordf_orders_filters">
		<input type="hidden" name="page" value="<?php echo WORDF_ADMIN_PAGE_SLUG;?>" />
		<input type="hidden" name="woordf_filters" value="1" />
	    <fieldset class="aditional_filters">
	    	<legend><?php _e('Filter orders',WORDF_LANG);?></legend>
	   		<div class="contaner">
	   			<div class="row">
			    	<div class="col-md-2">
			    		<label><?php _e('Search by label',WORDF_LANG);?></label>
			    		<input type="text" name="slabel" value="<?php echo (isset($_GET['slabel']))?$_GET['slabel']:'';?>" placeholder="<?php _e('Write search keyword',WORDF_LANG);?>" />
			    	</div>
	    	<?php

	    	foreach($filters as $filter):
	    		$field_name=$filter['name'];

	    	?>
	    	
	    		<div class="col-md-2">
		        	<label><?php echo $filter['label'];?></label>
		        	<select name="<?php echo $field_name;?>">
		        		<?php
		        		$selected=(!isset($_GET[$field_name]) || (isset($_GET[$field_name]) && $_GET[$field_name]==''))?' selected':'';
		        		?><option value=""<?php echo $selected;?>><?php _e('Choose',WORDF_LANG);?></option><?php
		        		foreach($filter['data'] as $key=>$value){
		        			
		        			$selected=(isset($_GET[$field_name]) && $_GET[$field_name]!='' && (int)$_GET[$field_name]==$key)?' selected':'';
							?><option<?php woordf_selected($field_name,$key);?> value="<?php echo $key;?>"<?php echo $selected;?>><?php echo $value;?></option><?php

						}
						
		        		?>
		        	</select>
	    		</div>
	        
	    	<?php
	    	endforeach;
	    	?>
	    			<div class="col-md-12">
	    				<?php submit_button( __('Filter',WORDF_LANG),'primary',NULL);?>
	    			</div>
	    		</div>

	   		</div>
	    	


	    </fieldset>

	<hr />	
	</form>
	<div id="<?php echo 'woordf_orders_form';?>">
	    <form method="post">
	    <?php $table->display(); ?>
	    </form>
	</div> 
</div> 
