(function($){
$(document).ready(function(){
    //$(".pretty").prettyPhoto();
    $("a[rel^='prettyPhoto']").prettyPhoto({social_tools:''});
  });
  /*
 $(document).on('click', '.prettyphoto', function(event) {
	event.preventDefault();
	$(this).prettyPhoto();
});
*/
})(jQuery);