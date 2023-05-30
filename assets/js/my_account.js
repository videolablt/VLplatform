(function($){  
	var woordf_view_files_modal=$('#woordf_view_files_modal');
	var first_shown=false;
	//copy to clipboard 
	function woordf_copy_to_clipboard(element){
		element.select();
		document.execCommand("copy");
	}
	//change url 
	function woordf_change_url(page, title, url) {
		if ("undefined" !== typeof history.pushState) {
			history.pushState({page: page}, title, url);
		} else {
			window.location.assign(url);
		}
	}
	//download file 
	function woordf_download(url) {
	  const a = document.createElement('a')
	  a.href = url
	  a.download = url.split('/').pop()
	  document.body.appendChild(a)
	  a.click()
	  document.body.removeChild(a)
	}
	//show all products files modal 
	function woordf_show_files_modal(oid){

		woordf_view_files_modal.modal('show');

		var container=woordf_view_files_modal.find('.modal-body');
		container.html('&nbsp;').addClass('loading');

		var attrs={
			action:'woordf_get_order_files',
			order_id:oid,
		}

        $.ajax({
            type: 'POST',
            url: woordf_vars.ajax_url,
            data: attrs,
            success: function(resp) {
                //console.log(resp);
                container.removeClass('loading').html(resp);
                if(woordf_vars.rid!=0 && !first_shown){
                	first_shown=true;
					woordf_show_record_files(woordf_vars.rid);
				}

            },
            error: function(err) {
            	container.removeClass('loading');
                console.log(err)
            }
        });
	}
	function woordf_hide_active_panels(){
		var container=woordf_view_files_modal.find('.modal-body');
		var active=container.find('.panel.active');
		if(active.length) active.fadeOut(300).removeClass('active');
	}
	//show one record files
	function woordf_show_record_files(rid){

		woordf_view_files_modal.modal('show');
		var container=woordf_view_files_modal.find('.modal-body');
		var all_products_block=container.find('.all-products-block');
		var one_record_block=container.find('.record-block');
		woordf_hide_active_panels();
		container.addClass('loading');

		var attrs={
			action:'woordf_get_record_files',
			record_id:rid,
		}

        $.ajax({
            type: 'POST',
            url: woordf_vars.ajax_url,
            data: attrs,
            success: function(resp) {
                //console.log(resp);
                container.removeClass('loading');
                one_record_block.html(resp);
                one_record_block.fadeIn(300).addClass('active');

            },
            error: function(err) {
            	container.removeClass('loading');
            	one_record_block.fadeIn(300).html(resp);
                console.log(err)
            }
        });
	}
  $(document).ready(function() {
		const element = document.getElementById("woordf_view_files_modal"); 
		//element.addEventListener("contextmenu", (event) => { event.preventDefault();});
  	
  	
  		woordf_view_files_modal=$('#woordf_view_files_modal');
		if(woordf_vars.oid!=0){
			woordf_show_files_modal(woordf_vars.oid);
		}
		woordf_view_files_modal.on('hidden.bs.modal', function () {
		  woordf_change_url("page", "page", woordf_vars.downloads_url);	
		});
  });
  	//view order files 
  
	$(document).on('click tap', '.view_order_files', function(event) {
		event.preventDefault();
		var oid=$(this).attr('data-order');
		woordf_change_url("page", "page",$(this).attr('href'));	
		woordf_show_files_modal(oid);

	});
	
	//view record files 
	$(document).on('click tap', '.view_record_files', function(event) {
		event.preventDefault();
		var rid=$(this).attr('data-id');
		woordf_change_url("page", "page",$('.view_order_files').attr('href')+'&rid='+rid);
		woordf_show_record_files(rid);

	});
	

	//back to list 
	$(document).on('click tap', '.modal .back-to-list', function(event) {
		event.preventDefault();
		var container=woordf_view_files_modal.find('.modal-body');
		var all_products_block=container.find('.all-products-block');
		var one_record_block=container.find('.record-block');
		container.find('.woordf-grid.photos').show();
		woordf_change_url("page", "page",$('.view_order_files').attr('href'));
		one_record_block.fadeOut(300,function(){
			one_record_block.removeClass('active');
			all_products_block.fadeIn(300).addClass('active');
		});
	});

	//generate download 
	$(document).on('click tap', '.modal .archive_files', function(event) {
		
		var th=$(this);
		var href=th.attr('href');
		if(href=='#'){
			event.preventDefault();
			var fname=th.attr('data-fname');
			var container=woordf_view_files_modal.find('.modal-body');
			var wait_block=container.find('.wait-block');
			var all_products_block=container.find('.all-products-block');
			var one_record_block=container.find('.record-block');
			woordf_hide_active_panels();
			wait_block.fadeIn(300);
			var attrs={
				action:'woordf_archive_files',
				folder_name:fname,
				return_link:1
			}

	        $.ajax({
	            type: 'POST',
	            url: woordf_vars.ajax_url,
	            data: attrs,
	            success: function(resp) {
	                wait_block.hide();
	                one_record_block.fadeIn(300);
	                th.attr('href',resp);
	                woordf_download(resp);

	            },
	            error: function(err) {
	                wait_block.hide();
	                one_record_block.fadeIn(300);
	                console.log(err)
	            }
	        });
		}
	});
	
	//rate vip record
	$(document).on('click tap', '.modal .rate-button', function(event) {
		event.preventDefault();
		var container=woordf_view_files_modal.find('.modal-body');
		var comments_visible=container.find('.user-comments.active').length;
		container.find('.rate-record').fadeIn(300);
		container.find('.woordf-single').fadeOut(300);
		container.find('.toolbar').fadeOut(300);
		container.find('.form.rate').show();
		container.find('.form.after-rate').hide();
		if(comments_visible) container.find('.user-comments').removeClass('active').hide();
	});
	
	//embed 
	
	$(document).on('click tap', '.modal .embed-button', function(event) {
		event.preventDefault();
		var container=woordf_view_files_modal.find('.modal-body');
		if(container.find('.woordf-single').length){
			container.find('.woordf-single').fadeOut(300,function(){
				container.find('.embed-file').fadeIn(300);
			});
		}
		else{
			container.find('.woordf-grid.photos').fadeOut(300,function(){
				container.find('.embed-file').fadeIn(300);
			});
		}

		
		container.find('.rate-record').hide();
		container.find('.form.after-rate').hide();

	});
	
	//copy embed code 
	
	$(document).on('click tap', '.modal .copy-embed', function(event) {
		event.preventDefault();
		var target_id=$(this).attr('data-target');
		var parent=$(this).parent();
		var target=$(target_id);
		if(target.length){
			woordf_copy_to_clipboard(target);
			parent.find('.response-text').slideDown(300);
			setTimeout(function(){parent.find('.response-text').slideUp(300);},3000);
		}

	});
	//rate radio
	$(document).on('click tap', ".woordf-rate .option", function(event) {
		event.preventDefault();
		var th=$(this);
		var val=th.attr('data-value');
		var container=woordf_view_files_modal.find('.modal-body');
		var comments_visible=container.find('.user-comments.active').length;
		if($(".woordf-rate .option.selected").length) $(".woordf-rate .option.selected").removeClass('selected');
		th.addClass('selected');
		if(!comments_visible) container.find('.user-comments').addClass('active').fadeIn(300);

    });
    
    //rate-product
    
	$(document).on('click tap', ".rate-product", function(event) {
		event.preventDefault();
		var th=$(this);
		var container=woordf_view_files_modal.find('.modal-body');
		var current_record_id=container.find('.rate-button').attr('data-id');
		var user_comment=container.find('.user_comment').val();
		if(user_comment=='') container.find('.user-comments .error-message').fadeIn(300);
		else{
			 container.find('.user-comments .error-message').fadeOut(300);
			var attrs={
				action:'woordf_add_log',
				record_id:current_record_id,
				type:'rate',
				user_id:th.attr('data-user'),
				value:container.find('.woordf-rate .option.selected').attr('data-value'),
				comment:user_comment
			}

			container.find('.form.rate').addClass('loading');
	        $.ajax({
	            type: 'POST',
	            url: woordf_vars.ajax_url,
	            data: attrs,
	            success: function(resp) {
					var data=JSON.parse(resp);
					container.find(".logs table tbody").append(data.content);
					if(container.find(".logs .empty-table-label").length) container.find(".logs .empty-table-label").remove();
					if(container.find(".logs .log-table.hidden").length) container.find(".logs .log-table.hidden").removeClass('hidden').fadeIn(300);

					
					container.find('.form.rate').removeClass('loading').fadeOut(300,function(){
	               		container.find('.form.after-rate').fadeIn(300).addClass('success');
	               		container.find('.success_message').html('<p class="success">'+data.message+'</p>');

					});
	            },
	            error: function(err) {
	                container.find('.form.rate').removeClass('loading');
	                console.log(err)
	            }
	        });
		}
    });
})(jQuery);