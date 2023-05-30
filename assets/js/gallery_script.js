  (function($){  
  function woordf_counter(event){

	   var element   = event.target;         // DOM element, in this example .owl-carousel
	    var items     = event.item.count;     // Number of items
	    var item      = event.item.index + 1;     // Position of the current item
	  
	  // it loop is true then reset counter from 1
	  if(item > items) {
	    item = item - items
	  }
	  $('#counter').html(item+" / "+items);

  }
    $(document).ready(function() {
 
		var $sync1 = $("#photo_row_1"),
		$sync2 = $("#photo_row_2.thumbs");

		var flag = false;
		var duration = 400;
	if($sync1.length)
	
		$sync1.owlCarousel({
			items: 1,
			margin: 10,
			nav: true,
			dots: false,
			onInitialized  : woordf_counter,
			onTranslated : woordf_counter,
			navText:[
			      '',
			      ''
			]
		})
		.on('changed.owl.carousel', function (e) {
			if (!flag) {
				flag = true;
				$sync2.trigger('to.owl.carousel', [e.item.index, duration, true]);
				flag = false;
			}
		});
if($sync2.length)
	$sync2.owlCarousel({
			margin: 12,
			items: 6,
			center: false,
			nav:false,
			dots: false,
			responsiveClass:true,
		    responsive:{
			        0:{
			            items:4
			        },
			        480:{
			            items:4
			        },
			        768:{
						items:4
					},
			        980:{
			            items:6
			        },
			        1199:{
			            items:8
			        }
			},
			navText:[
			      '',
			      ''
			]
		})
		.on('click', '.owl-item', function () {
			$sync1.trigger('to.owl.carousel', [$(this).index(), duration, true]);

		})
		.on('changed.owl.carousel', function (e) {
			if (!flag) {
				flag = true;		
				$sync1.trigger('to.owl.carousel', [e.item.index, duration, true]);
				flag = false;
			}
		});


		$(document).keyup(function(event) {
		     if (event.keyCode == 37) {
		       $sync1.trigger('prev.owl.carousel', [duration]);
		    } else if (event.keyCode == 39) {
		       $sync1.trigger('next.owl.carousel', [duration]);
		    }
		});
     
    });

})(jQuery);