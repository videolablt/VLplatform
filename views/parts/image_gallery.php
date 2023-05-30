<?php
$files=woord_directory_handler::get_folder_content_list($record['folder_name'],'img');
?><div class="woordf-grid photos"><?php
foreach($files as $key=>$file){
	include WORDF_DIR.'/views/loop/grid_item_photos.php';	 
}
?>
</div>
<script>

var wp_openPhotoSwipe = function(ind) {
    var pswpElement = document.querySelectorAll('.pswp')[0];
    var items = [<?php woordf_output_file_info_for_js($files);?>];

    var options = {       
        history: false,
        focus: false,
		index:ind,
		zoomEl: true,
		clickToCloseNonZoomable: false,
        showAnimationDuration: 0,
        hideAnimationDuration: 0
        
    };
    
    var gallery = new PhotoSwipe( pswpElement, PhotoSwipeUI_Default, items, options);
    gallery.init();
};		
 (function($){  
		$(document).on('click', '.gallery_item', function(event) {
			event.preventDefault();
			var ind=parseInt($(this).attr('data-index'));
			wp_openPhotoSwipe(ind);
		});	

})(jQuery);
</script>
