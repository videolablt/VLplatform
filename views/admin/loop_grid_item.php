<div class="item">

<?php if(($type!=1 && $valid_image) || $type==1):?>
	<?php woordf_output_icon('trash',__('Remove file',WORDF_LANG));?>
	<a data-index="<?php echo ($ind++);?>" class="gallery_item" href="<?php echo $file; ?>">
<?php endif;?>
	<div <?php woordf_admin_item_thumbnail_attrs($record,$file);?>>
		<span class="fname"><?php echo $fname;?></span><?php
		if(woordf_get_file_extension($file)=='zip') woordf_output_icon('clip');
		else woordf_output_icon(woordf_icon_by_type_and_key('type',$type)); 
	?></div>
	<?php
	 
	
	?>
<?php if(($type!=1 && $valid_image) || $type==1):?>
	</a>
<?php endif;?>
</div>
