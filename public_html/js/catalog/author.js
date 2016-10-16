
$(document).ready(function() {

	//$('.page-list-nav').columnize({ columns: 5 }); // prolly needs rebind

	var search_category = 'author';
	var search_letter	= 'a';

	var search_page		= 1;
	var search_status  	= 'complete';
	var author_id 		= $('#author_id').val();


	$('.search_category').live('click', function(){
		search_category = $(this).attr('data-category');
		redirect_to_search(search_category);
		return false;
	});

	$('.alpha-button').live('click', function(){
		search_letter = $(this).attr('data-letter');
		redirect_to_search(search_category);
		return false;
	});


	function redirect_to_search()
	{
		window.location.href = CI_ROOT + 'search/'+search_category;
	}



	$('.page-number').live('click', function(){
		search_page = $(this).attr('data-page_number');		
		get_results('author/'+ author_id);
		return false;
	});

	$('.project_status').live('click', function(){
		search_status = $(this).attr('data-status');
		search_page		= 1;  //reset for other tab
		get_results('author/'+ author_id);
		return false;
	});

	//we need to do for page numbers also

	function get_results(url)
	{
		$.ajax({
		  	url: CI_ROOT + url,
		  	type: 'post',
		  	data: {'search_page':search_page, 'search_status':search_status},
	      	complete: function(r){
			var response_obj = jQuery.parseJSON(r.responseText);
				
				if (response_obj.status == 'SUCCESS')
				{
					$('#block_one').html(response_obj.results);
					$('#pagination').html(response_obj.pagination);
				}
			}
		});		
	}
			
});


