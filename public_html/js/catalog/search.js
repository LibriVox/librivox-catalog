

$(document).ready(function() {

	$("#more-or-less a").click(function() {
		toggle_more_or_less()
		
	});

	function toggle_more_or_less()
	{
		var txt = $("ul.page-list-nav").is(':visible') ? 'open' : 'close';
		$("#more-or-less a").text(txt).toggleClass('arrow-down');
		$("ul.page-list-nav").slideToggle();
	}

	//$('.page-list-nav').columnize({ columns: 5 }); // prolly needs rebind

	var search_category = get_current_catagory();
	var search_letter	= 'a'; 
	var search_q		= $('#search_q').val();
	var search_page		= 1;
	var search_results_tab = 'all';

	$('.search_category').live('click', function(){
		search_category = $(this).attr('data-category');
		clear_search_q();
		redirect_to_search();

	});

	$('.js-genre_button').live('click', function(){
		search_category = 'genre';
		search_letter	= $(this).attr('data-genre_id');
		//console.log(search_letter);
		clear_search_q();
		get_results();
		highlight_category_link('genre');	
		//toggle_more_or_less();	
	});	

	$('.js-language_button').live('click', function(){
		search_category = 'language';
		search_letter	= $(this).attr('data-language_id');
		clear_search_q();
		get_results();
		highlight_category_link('language');	
		toggle_more_or_less();	
	});

	$('.js-alpha_button').live('click', function(){
		search_letter = $(this).attr('data-letter');	
		search_page		= 1;	
		clear_search_q();
		get_results();
		highlight_category_link(search_category);
		highlight_alpha_link(search_letter);
		return false;
	});

	$('.js_results_tab').live('click', function(){
		search_page		= 1;
		search_results_tab = $(this).attr('data-category');	
		get_results();
		highlight_results_tab_link(search_results_tab);
		return false;
	});

	$('.page-number').live('click', function(){
		search_page = $(this).attr('data-page_number');
		get_results();
		return false;
	});

	function redirect_to_search()
	{
		window.location.href = CI_ROOT + 'search/'+search_category;
	}


	function get_current_catagory()
	{	
		search_category = $('.white').attr('data-category');
		//console.log(search_category);
		return search_category;
	}

	//we need to do for page numbers also

	function get_results()
	{
		//console.log('get results');
		$.ajax({
		  	url: CI_ROOT + 'search',
		  	type: 'post',
		  	data: {'search_category':search_category, 'search_q':search_q,  'search_letter':search_letter, 'search_page':search_page, 'search_results_tab': search_results_tab},
	      	complete: function(r){
				var response_obj = jQuery.parseJSON(r.responseText);
				
				//console.log(response_obj.status);
				if (response_obj.status == 'SUCCESS')
				{					
					$('#block_one').html(response_obj.results);
					$('#pagination').html(response_obj.pagination);
					$('#breadcrumbs').html(response_obj.breadcrumbs);
					$('#browsing_label').html(response_obj.browsing_label);
					$('#genre_menu_breadcrumbs').html(response_obj.genre_menu_breadcrumbs);

					rebuild_genre_menu(response_obj.genre_menu);
				}
			}
		});		
	}
			
	function highlight_category_link(search_category)
	{
		$('.search_category').removeClass('white');
		$('.search_category[data-category='+search_category+']').addClass('white');
	}

	function highlight_alpha_link(search_letter)
	{
		$('.alpha-button').removeClass('white');
		$('.alpha-button[data-letter='+search_letter+']').addClass('white');
	}

	function highlight_results_tab_link(search_results_tab)
	{
		$('.js_results_tab').removeClass('white');
		$('.js_results_tab[data-category='+search_results_tab+']').addClass('white');		
	}

	function clear_search_q()
	{
		search_q = '';
		$('#search_q').val('');
	}

	function rebuild_genre_menu(genre_menu)
	{
		var html = '';
		jQuery.each(genre_menu, function(index, value){
			html = html + '<li class="list-button js-genre_button" data-genre_id="'+ value.id +'"><a href="#">'+ value.name +'</a></li>';
		});

		$('.page-list-nav').html(html);
	}

});
