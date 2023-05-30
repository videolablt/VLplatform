<div class="container">
	<div class="row">
		<div class="col-md-12 video-wrapper">
			<video width="100%" autoload controls="true" muted="false">
				<source src="<?php echo $video_files[0];?>" type="video/mp4" />
				Your browser does not support the video tag.
			</video> 
		</div>
	</div>
</div>
<?php

if(count($video_files)>0){
	?>
	<div class="item-nav left">
		<?php woordf_output_icon('angle-left');?>
	</div>
	<div class="item-nav right">
		<?php woordf_output_icon('angle-right');?>
	</div>
	<div class="counter"><?php echo '1 / '.count($video_files);?></div>

<script>


 (function($){  
	var wrapper,video_wrapper,cind,start_height,videos;
	function woordf_video_show_admin_modal(video_url){
		var woordf_admin_modal=$('#woordf_admin_modal');
		woordf_admin_modal.modal('show');
	}
	function woordf_change_video(ind){
		cind=ind;
		var src=videos[cind];
		var video='<video width="100%" autoload controls="true" muted="false"><source src="'+src+'" type="video/mp4" />Your browser does not support the video tag.</video>';
		video_wrapper.html(video);
		if((cind-1)<0 && (!wrapper.find('.item-nav.left').hasClass('active'))) wrapper.find('.item-nav.left').removeClass('active');
		else wrapper.find('.item-nav.left').addClass('active');
		if((cind+1)==videos.length && (wrapper.find('.item-nav.right').hasClass('active'))) wrapper.find('.item-nav.right').removeClass('active');
		else wrapper.find('.item-nav.right').addClass('active');
		
		wrapper.find('.counter').html((cind+1)+' / '+videos.length);
	}
	function woordf_video_by_filename(file_name){
		var res=0;
		$.each(videos , function(i, val) { 
	  		if(val.indexOf(file_name) !== -1) res=i;
	  		console.log(val+' fn: '+file_name+' ind: '+val.indexOf(file_name));
		});
		if(res!=0) woordf_change_video(res);
	}
	//view video
	$(document).on('click tap', '.files_list .video', function(event) {
		event.preventDefault();
		console.log('video');
		woordf_video_show_admin_modal();
		var fname=$(this).closest('.it-inner').attr('data-file');
		woordf_video_by_filename(fname);
	});
    $(document).ready(function() {
    	cind=0;
    	wrapper=$('#woordf_admin_modal .record-block');
    	video_wrapper=$('#woordf_admin_modal .record-block .video-wrapper');
    	start_height=video_wrapper.innerHeight();
    	video_wrapper.css('min-height',start_height+'px');
    	var video=video_wrapper.find('video');
    	videos=["<?php echo implode('","',$video_files)?>"];
    	wrapper.find('.item-nav.right').addClass('active');
    	
		$('body').on('click', '#woordf_admin_modal .item-nav', function(e){
			var side=$(this).hasClass('left')?'left':'right';
			video_wrapper.addClass('loading');
			if(side=='right'){
				cind++;
			}
			else{
				cind--;

			}
			woordf_change_video(cind);
			

			
		});
    });

})(jQuery);
</script>
<?php
}
?>