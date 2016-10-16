$(document).ready(function() {

	//$('.page-list-nav').columnize({ columns: 5 }); // prolly needs rebind

	var search_category = 'author';
	var search_letter	= 'a';

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

	$('.js-genre_button').live('click', function(){
		search_category = 'genre';
		search_letter	= $(this).attr('data-genre_id');

		redirect_to_search();



		
		//console.log(search_letter);
		//clear_search_q();
		//get_results();
		//highlight_category_link('genre');	
		//toggle_more_or_less();	
	});

	function redirect_to_search()
	{
		window.location.href = CI_ROOT + 'search/'+search_category + '/en/' + search_letter;
	}


});