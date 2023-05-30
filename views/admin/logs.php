
<div class="woordf-logs-wrapper">
<h1><?php _e('Logs',WORDF_LANG);?></h1>
<form action="<?php echo admin_url( 'admin.php?'); ?>" method="GET" class="woordf_orders_filters">
	<input type="hidden" name="page" value="<?php echo WORDF_ADMIN_LOGS_PAGE_SLUG;?>" />
	<input type="hidden" name="woordf_filters" value="1" />
    <fieldset class="aditional_filters">
    	<legend><?php _e('Filter logs',WORDF_LANG);?></legend>
   		<div class="contaner">
   			<div class="row">
		    	<div class="col-md-2">
		    		<label><?php _e('Search in commment',WORDF_LANG);?></label>
		    		<input type="text" name="keyword" value="<?php echo (isset($_GET['keyword']))?$_GET['keyword']:'';?>" placeholder="<?php _e('Write search keyword',WORDF_LANG);?>" />
		    	</div>
		    	<div class="col-md-2">
		    		<label><?php echo __('Date',WORDF_LANG).' '.__('from',WORDF_LANG);?>:</label>
		    		<input type="text" name="date_from" class="date-picker" value="<?php echo (isset($_GET['date_from']))?$_GET['date_from']:'';?>" placeholder="<?php echo __('Date',WORDF_LANG).' '.__('from',WORDF_LANG);?>" />
		    	</div>
		    	<div class="col-md-2">
		    		<label><?php echo __('Date',WORDF_LANG).' '.__('to',WORDF_LANG);?>:</label>
		    		<input type="text" name="date_to" class="date-picker" value="<?php echo (isset($_GET['date_to']))?$_GET['date_to']:'';?>" placeholder="<?php echo __('Date',WORDF_LANG).' '.__('to',WORDF_LANG);?>" />
		    	</div>
    			<div class="col-md-12">
    				<?php submit_button( __('Filter',WORDF_LANG),'primary',NULL);?>
    			</div>
    		</div>

   		</div>
    	


    </fieldset>

<hr />	
</form>
    <form method="post" class="admin-logs">
    	<?php $table->display(); ?>
    </form>
</div>
<script>
jQuery(function($){
      $.datepicker.regional['lt'] = {
      			clearText: 'Išvalyti', clearStatus: '',
                closeText: 'UŽdaryti', closeStatus: '',
                prevText: '&lt;Atgal',  prevStatus: '',
                nextText: 'Pirmyn&gt;', nextStatus: '',
                currentText: "&#352;iandien", currentStatus: '',
                monthNames: ['Sausis','Vasaris','Kovas','Balandis','Gegu&#382;&#279;','Bir&#382;elis','Liepa','Rugpj&#363;tis','Rugs&#279;jis','Spalis','Lapkritis','Gruodis'],
                monthNamesShort: ['Sau','Vas','Kov','Bal','Geg','Bir',
                'Lie','Rugp','Rugs','Spa','Lap','Gru'],
                monthStatus: '', yearStatus: '',
                weekHeader: '', weekStatus: '',
                dayNames: ['sekmadienis','pirmadienis','antradienis','tre&#269;iadienis','ketvirtadienis','penktadienis','&#353;e&#353;tadienis'],
                dayNamesShort: ['sek','pir','ant','tre','ket','pen','&#353;e&#353;'],
                dayNamesMin: ['Sk','Pr','An','Tr','Kt','Pn','&#352;t'],
                dayStatus: 'DD', dateStatus: 'D, M d',
                dateFormat: 'yy-mm-dd', firstDay: 1,
				timeOnlyTitle: 'Pasirinkite laiką',
				timeText: 'Laikas',
				hourText: 'Valanda',
				minuteText: 'Minutė',
				secondText: 'Sekundė',
				timezoneText: 'Laiko juosta',
                initStatus: '', isRTL: false};
        $.datepicker.setDefaults($.datepicker.regional['lt']);
});
(function($){
	
	$(document).ready(function()
	{
		$('.date-picker').datepicker($.extend({}, $.datepicker.regional['lt'], {
			dateFormat: 'yy-mm-dd'
		}));
	});
})(jQuery);	
</script>