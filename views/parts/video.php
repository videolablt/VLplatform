<div class="container">
	<div class="row">
		<div class="col-md-12 video-wrapper">
			<video id="woordf_video" width="100%" autoload controls="true" muted="false">
				<source src="<?php echo $files[0];?>" type="video/mp4" />
				Your browser does not support the video tag.
			</video> 
		</div>
	</div>
</div>
<?php
if(count($files)>1){
	?>
	<div class="item-nav left" title="<?php _e('Previos video', WORDF_LANG);?>">
		<?php woordf_output_icon('angle-left');?>
	</div>
	<div class="item-nav right" title="<?php _e('Next video', WORDF_LANG);?>">
		<?php woordf_output_icon('angle-right');?>
	</div>
	<div class="counter"><?php echo __('You are currently viewing', WORDF_LANG).' ';?><b><?php echo '1 / '.count($files);?></b><?php echo ' '.__('videos', WORDF_LANG);?></div>

<script>


 (function($){  
	var wrapper,video_wrapper,cind,start_height;
	
	function change_video(src){
		var video='<video id="woordf_video" width="100%" autoload controls="true" muted="false"><source src="'+src+'" type="video/mp4" />Your browser does not support the video tag.</video>';
		video_wrapper.html(video);
		
	}
	function woordf_responsive_video(){
		var video=$('#woordf_video');
		var video_height=video.height();
		if($('body.embed').length){
			if($(window).height()<video_height){
				video.css('height',$(window).height()+'px');
				video.css('width','auto');

			}
			else{
				video.css('width','100%');
				video.css('height','auto');
			}
			
		}

	}
    $(document).ready(function() {
    	cind=0;
    	wrapper=$('#woordf_view_files_modal .record-block');
    	video_wrapper=$('#woordf_view_files_modal .record-block .video-wrapper');
    	start_height=video_wrapper.innerHeight();
    	var video=video_wrapper.find('video');
    	var videos=["<?php echo implode('","',$files)?>"];
    	wrapper.find('.item-nav.right').addClass('active');
    	woordf_responsive_video();
		$('body').on('click', '#woordf_view_files_modal .item-nav', function(e){
			var side=$(this).hasClass('left')?'left':'right';
			video_wrapper.addClass('loading');
			if(side=='right'){
				cind++;
				if((cind+1)==videos.length) $(this).removeClass('active');
				if(!wrapper.find('.item-nav.left').hasClass('active')) wrapper.find('.item-nav.left').addClass('active');
			}
			else{
				cind--;
				if((cind-1)<0) $(this).removeClass('active');
				if(!wrapper.find('.item-nav.right').hasClass('active')) wrapper.find('.item-nav.right').addClass('active');
			}
			change_video(videos[cind]);
			wrapper.find('.counter b').html((cind+1)+' / '+videos.length);
			woordf_responsive_video();

			
		});
    });
	$( window ).resize(function() {
		woordf_responsive_video();
	});
})(jQuery);
</script>
<?php
}
?>