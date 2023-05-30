
<div class="item single">
	<div class="inner">
		<div <?php woordf_grid_item_thumbnail_attrs($record,$file);?>>
			<a data-index="<?php echo $key;?>" class="gallery_item" href="<?php echo $file; ?>">
				<?php woordf_output_icon(woordf_icon_by_type_and_key('type',$record['type']));?>
			</a>
		</div>
	</div>
</div>

