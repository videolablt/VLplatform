<div class="item">
	<div class="inner">
		<div <?php woordf_grid_item_thumbnail_attrs($record);?>><?php woordf_output_icon(woordf_icon_by_type_and_key('type',$record['type']));?></div>
		<div class="type"><?php 
			woordf_output_icon(woordf_icon_by_type_and_key('type',$record['type'])); 
			echo woordf_get_type_label($record['type']);?>
			
		</div>
		<div class="label"><?php echo ($record['label']!='')?$record['label']:woordf_get_type_label($record['type']);?></div>
		<div class="actions">
			<?php 
				echo '<a '.woordf_grid_item_preview_button_attrs($record).'>'.__('Preview files',WORDF_LANG).'</a>';
				if($record['remote']){
					woordf_remote_download_button($record);
				}
			?>
			
		</div>
	</div>
</div>
