

$('#advanced_search_form_submit').on('click', function(e){
	e.preventDefault();

	//console.log('clicked');
	

	//console.log('get results');
	$.ajax({
	  	url: CI_ROOT + 'advanced_search',
	  	type: 'post',
	  	data: $('#advanced_search_form').serialize(),
      	complete: function(r){
		var response_obj = jQuery.parseJSON(r.responseText);
			
			//console.log(response_obj.status);
			if (response_obj.status == 'SUCCESS')
			{					
				$('#block_one').html(response_obj.results);
				$('#pagination').html(response_obj.pagination);
			}
		}
	});


});