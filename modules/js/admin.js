jQuery(document).ready(function($){

	$('body').on('click', '.apply_cf', function(e){
		var href = $('.apply_cf').attr('href');
		href = href.replace('%FROM%', $('#post_to_copy_from').val() ) ;
		$('.apply_cf').attr('href', href);
		console.log( href );
 
	})

	if( $('.select2').length > 0 ){
		$('.select2').select2();	 
	}

});