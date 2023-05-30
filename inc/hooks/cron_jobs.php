<?php
	add_action( 'wp', 'woordf_shedule' );
	add_action( 'woordf_hourly_event', 'woordf_remove_expired_data_hourly' );

	function woordf_shedule() {
		/*
	    if ( !wp_next_scheduled( 'woordf_daily_event' ) ) {
	        wp_schedule_event( time(), 'daily', 'woordf_daily_event' );
	    }
	    */
	    if ( !wp_next_scheduled( 'woordf_hourly_event' ) ) {
	        wp_schedule_event( time(), 'hourly', 'woordf_hourly_event' );
	    }
	}

	function woordf_remove_expired_data_hourly() {
		woordf_cron_jobs_action(false);
	}
	function woordf_remove_expired_data_manual() {
		woordf_cron_jobs_action(true);
	}
	
	function woordf_cron_jobs_action($manualy=true){
		$db_actions=new woordf_records_module();
		//options

		$log_report_days_before = get_option(WORDF_PX.'log_report_days_before');
		$email_customer_days_before_enabled = get_option(WORDF_PX.'email_customer_days_before_enabled');
		$email_admin_days_before_enabled = get_option(WORDF_PX.'email_admin_days_before_enabled');
		$email_customer_after_remove_enabled = get_option(WORDF_PX.'email_customer_after_remove_enabled');
		$email_admin_after_remove_enabled = get_option(WORDF_PX.'email_admin_after_remove_enabled');

		
		
		$removed_records_data=$db_actions->remove_expired();
		$soon_expires_records_data=$db_actions->get_soon_expires_data($log_report_days_before);
		
		$logs_db_actions=new woordf_logs_module();
		

		if(!empty($removed_records_data)){
			foreach($removed_records_data as $data){
				if($logs_db_actions->not_exist($data)){
					$logs_db_actions->insert_row($data);
					if($email_customer_after_remove_enabled) woordf_send_mail('customer','expired',$data);
					if($email_admin_after_remove_enabled) woordf_send_mail('admin','expired',$data);
				}
			}
		}
		if(!empty($soon_expires_records_data)){
			foreach($soon_expires_records_data as $data){
				if($logs_db_actions->not_exist($data)){
					$logs_db_actions->insert_row($data);
					if($email_customer_days_before_enabled) woordf_send_mail('customer','days_before',$data);
					if($email_admin_days_before_enabled) woordf_send_mail('admin','days_before',$data);
				}
			}
		}
		if(!$manualy){
			file_put_contents(dirname(__FILE__).'/logai.txt','hourly event fired; soon expires:'. var_export($soon_expires_records_data,true).PHP_EOL.'expired:'. var_export($removed_records_data,true).PHP_EOL, FILE_APPEND | LOCK_EX);			
		}

	}