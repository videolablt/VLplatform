<div class="resp-wrapper">
	<div class="inner">
		<div
		   id="image360_<?php echo $record['folder_name'];?>"
		   data-folder="<?php echo WORDF_UPLOADS_FOLDER_URL.$record['folder_name'].'/';?>" <?php echo implode(" ",$attributes);?>
		></div>
	</div>
</div>
<script src="<?php echo WORDF_URL.'libs/image360/cloudimage360.js';?>"></script>
<script>
function add360View_<?php echo $record['folder_name'];?>(viewId) {
	const new360View = document.getElementById(viewId);
	new360View.classList.add("cloudimage-360");
	window.CI360.add(viewId);
}
(function($){  
	var dims=[<?php echo (isset($image_dimensions['width']) && isset($image_dimensions['height']))? $image_dimensions['width'].','. $image_dimensions['height']:'';?>];
	
	
	function calc_width(dms){
		var aspr=dms[0]/dms[1];
		var w=$(window).height()*aspr;
		return w-10;
	}
	function recalculate_width(){
		if(dims.length){
			var width=calc_width(dims);
			width=($('.woordf-single').length)?(width*0.7) : width;
			width=($(window).width()<769)? $('.woordf-single').width() : width;
			$('.resp-wrapper .inner').css('width',width+'px');

		}
	}
	$().ready(function(){
		add360View_<?php echo $record['folder_name'];?>("image360_<?php echo $record['folder_name'];?>");
		recalculate_width();
		
	});	
	$( window ).resize(function() {
		recalculate_width();
	});

})(jQuery);
</script>
