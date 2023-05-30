
<div id="woordf_admin_modal" class="modal fade" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><?php woordf_output_icon('close');?></button>
			</div>
			<div class="modal-body">
				<div class="woordf-single record-block">	
					<?php if($type_slug!='image_360') include WORDF_DIR.'/views/admin/parts/'.$type_slug.'.php';?>
				</div>
			</div>
		</div>
	</div>
</div>
