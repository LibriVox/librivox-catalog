
	<footer class="footer-wrap">
		<div class="footer">
		
		   
			
			<p class="license clear"><a href="#"><img src="<?= base_url()?>images/public-domain-license.gif" alt="public-domain-license" width="88" height="31" /></a></p>
		</div><!-- end .footer-wrap -->	
	</footer>
    <!--[if lte IE 9]>	
	    <script type="text/javascript" src="<?= base_url()?>js/catalog/jquery.columnizer.js"></script>
     <![endif]-->



<script type="text/javascript">

	var search_category = "<?= $search_category; ?>";

	var sub_category;

	var advanced_search = "<?= empty($advanced_search)? 0 : $advanced_search; ?>";

	var primary_key = <?= $primary_key; ?>;

	var search_page  = "<?= empty($search_page)? 1 : $search_page; ?>";
	set_advanced_form_page(search_page);

	var search_order = 'alpha';

	var project_type = 'either';

	var q = "<?= empty($search_value)? '': $search_value; ?>";

	var spinner = '<div class="loading_img" style="margin-left:300px;margin-top:60px;"><img src="../../img/loading.gif"/></div>';

	$('.browse-header').hide();

	var pathArray = window.location.pathname.split( '/' );

	var current_page = pathArray[1]; 

	if (q != '')
	{
		advanced_search = 0;
		$('.advanced-search-inner').hide('slow');
		librivox_search();
	}
	else if (advanced_search == 1)
	{
		advanced_search_actions();
	}
	else if (advanced_search == 2)
	{
		advanced_form_submit();
	}
	else if (advanced_search == 3)
	{
		//may be able to combine with below
		$('.advanced-search-inner').hide();
		get_results(search_category, search_page, sub_category, primary_key);
	}
	else
	{	
		//only on loading search page
		if (current_page == 'search') 
		{
			var item = $('.js-menu_item[data-menu_item="'+search_category+'"]');
			load_search_data(item, search_category);
		}
		else if (jQuery.inArray(current_page, ['author', 'reader', 'group']) > -1)
		{
			get_results(current_page, search_page, sub_category, primary_key);
		}

	}	
	advanced_search = 0; // it's done its job


	// do better 
	$('#sort_type').hide();
	if (search_category == 'title' ||  search_category == 'reader')
	{
		$('#sort_type').show();
	}	

	$('#sort_menu').hide();
	if (search_category == 'title' || (primary_key > 0))
	{
		$('#sort_menu').show();
	}	


	// Manage menus
	$('.js-menu_item').on('click', function(e){

		search_category = $(this).attr('data-menu_item');

		primary_key = 0;

		if (current_page != 'search') 
		{
			window.location.href = CI_ROOT + 'search/' + search_category;
		}	

		e.preventDefault();

		$('.advanced-search-inner').hide('slow');
		$('#sidebar_wrapper').show();

		$('#sort_menu').hide();
		if (search_category == 'title' || (primary_key > 0))
		{
			$('#sort_menu').show();
		}	

		q = '';
		$('#q').val('');		
		
		load_search_data($(this), $(this).attr('data-menu_item'));

	});

	$('.js-title-submenu').on('click', function(e){
		e.preventDefault();
		$('.js-title-submenu a').removeClass('selected');
		$(this).children('a').addClass('selected');

		project_type = $(this).attr('data-submenu');

		set_advanced_form_page(1); //this is a new search, so reset

		get_results(search_category, search_page, sub_category, primary_key);
	});

	$('.js-sort-menu').on('change', function(){
		search_order = $(this).val();
		get_results(search_category, search_page, sub_category, primary_key);

	});


	function load_search_data(item, label)
	{
		$('.js-menu_item a').removeClass('active').removeClass('current-page');

		item.children('a').addClass('active').addClass('current-page');

		//console.log(item.attr('data-menu_item'));

		//global
		search_order = 'alpha';
		if (item.attr('data-menu_item') == 'title')
		{
			search_order = 'catalog_date';
		}

		$('.js-sort-menu').val(search_order);		

		get_results(search_category, 1);

		set_browse_header(label);

	}


	function set_browse_header(label)
	{
		label = label.charAt(0).toUpperCase() + label.slice(1);
		var text = 'Browsing <span>'+ label +'</span>';
		$('.browse-header').html(text).show();
		$('.browse-header-wrap').show();
	}


	function get_results(search_category, search_page, sub_category, primary_key)
	{
	    sub_category 	= typeof sub_category !== 'undefined' ? sub_category : '';
	    primary_key 	= typeof primary_key !== 'undefined' ? primary_key : 0;
	    search_order 	= typeof search_order !== 'undefined' ? search_order : 'alpha';
	    project_type 	= typeof project_type !== 'undefined' ? project_type : 'either';

		var params = { 'primary_key': primary_key, 'search_category':search_category, 'sub_category': sub_category ,'search_page':search_page, 'search_order': search_order, 'project_type': project_type} ;

		if (history.pushState && current_page == 'search') {
			history.pushState(null, location.textContent, location.href);
			history.replaceState(null, null, "<?= base_url().'search' ?>");
		}

		$.ajax({
		  	url: CI_ROOT + current_page + '/get_results' ,
		  	type: 'get', //yes, get...we want these all to work through the browser addressbar as well, now
		  	data: { 'primary_key': primary_key, 'search_category':search_category, 'sub_category': sub_category ,'search_page':search_page, 'search_order': search_order, 'project_type': project_type},
		  	beforeSend: function(){
		  		$('.browse-list').html(spinner);
		  		$('.page-number-nav').html('');
		  	}, 		  	
	      	complete: function(r){
				var response_obj = jQuery.parseJSON(r.responseText);

				$('.browse-list').html('');

				if (response_obj.status == 'SUCCESS')
				{				

			  		$('#sort_menu').hide();
			  		$('#sort_type').hide();


					if ((response_obj.results != 'No results') && (search_category != 'group') && (search_category == 'title' || (primary_key > 0)))
					{
						$('#sort_menu').show();	
						$('#sort_type').show();				
					}

					$('.browse-list').html(response_obj.results);
					$('.page-number-nav').html(response_obj.pagination);
				}

				if (history.pushState) {
					history.pushState(null, location.textContent, location.href);

					history.replaceState(null, null, "?primary_key=" + primary_key + '&search_category=' + search_category + '&search_page=' + search_page + '&search_form=get_results');	
					
				}


			}
		});		
	}


	/* Advanced search form function  */

	$('#advanced_search_form_submit').on('click', function(e){
		e.preventDefault();

		set_advanced_form_page(1); //this is a new search, so reset

		advanced_form_submit();
	});

	function set_advanced_form_page(new_page)
	{
		search_page = new_page;
		$('#search_page').val(new_page);
	}

	function advanced_form_submit()
	{
		$('.advanced-search-inner').hide('slow');

		$('#sort_type').hide();
		//console.log('hidden');	

		q = '';
		$('#q').val('');

		get_advanced_results();

		$('#sidebar_wrapper').show();		
	}

	function get_advanced_results()
	{

		$.ajax({
		  	url: CI_ROOT  + 'advanced_search',
		  	type: 'get',
		  	data: $('#advanced_search_form').serialize() + '&q=' + q ,
		  	beforeSend: function(){
		  		$('.browse-list').html(spinner);
		  		$('.page-number-nav').html('');
		  	}, 
	      	complete: function(r){
				var response_obj = jQuery.parseJSON(r.responseText);

				$('.browse-list').html(''); //clear the spinner, success or no
				
				//console.log(response_obj.status);

		  		$('#sort_menu').hide();
		  		$('#sort_type').hide();

				if (response_obj.status == 'SUCCESS')
				{					
					$('.browse-list').html(response_obj.results);
					$('.page-number-nav').html(response_obj.pagination);
										
					if (search_category == 'title' || (primary_key > 0))
					{
						$('#sort_menu').show();
					}	
				}


				if (history.pushState) {
					history.pushState(null, location.textContent, location.href);

					if (q != '')
					{
						history.replaceState(null, null, "?q=" + q + '&search_form=advanced');
					}	
					else
					{
						history.replaceState(null, null, "?" + $('#advanced_search_form').serialize());
					}	
					
				}

			}
		});
	}	


	$('.js-advanced-search').on('click', function(e){

		// TODO: make toggle

		if (current_page != 'search') 
		{
			window.location.href = CI_ROOT + 'search/' + 'advanced_search';
		}


		e.preventDefault();
		advanced_search_actions();
		return false;	

	});

	function advanced_search_actions()
	{

		$('.browse-header').html('');
		$('.browse-list').html('');			
		$('.page-number-nav').html('');
		$('#sidebar_wrapper').hide();
		$('#sort_menu').hide();

		$('.advanced-search-inner').show('slow');

	}


	/* end Advanced Search Form*/

	/* Librivox search form */

	$('#searchsubmit').on('click', function(e){
		e.preventDefault();

		//console.log(current_page);

		q = $('#q').val();

		if (current_page != 'search') 
		{
			window.location.href = CI_ROOT + 'search/q/' + q;
		}

		librivox_search();

		$('#sidebar_wrapper').show();
		$('.advanced-search-inner').hide('slow');

	});


	function librivox_search()
	{
		set_advanced_form_page(1); //this is a new search, so reset

		search_order = 'alpha';

		$('#advanced_search_form #sort_order').val('alpha'); // the code eventually serializes the form, so we need to set it to alpha here

		$('.browse-header-wrap').hide();

		get_advanced_results();
	}


	/* end Librivox search form*/


	$(document).on('click', '.page-number', function(e)
	{
		e.preventDefault();
		search_page = $(this).attr('data-page_number');
		var call_function = $(this).attr('data-call_function');

		if (call_function == 'get_advanced_results')
		{
			$('#search_page').val(search_page); //set it for the form.serialize()
			get_advanced_results();
		}
		else
		{
			get_results(search_category, search_page, sub_category, primary_key);
		}	
		
		return false;
	});

	$(document).on('click', '.js-sublink', function(e)
	{
		e.preventDefault();
		sub_category = $(this).attr('data-sub_category');
		primary_key = $(this).attr('data-primary_key');

		set_advanced_form_page(1); //this is a new search, so reset

		if (search_category == 'title' || (primary_key > 0))
		{
			$('#sort_menu').show();
		}

		var label = $(this).text();

		set_browse_header(label);

		get_results(search_category, search_page, sub_category, primary_key);

		return false;

	});	

	//reader sections

	$('.data_tab').on('click', function(){
		$('.data_tab').removeClass('selected');
		$(this).addClass('selected');

		$('.tab-pane').removeClass('selected').hide();

		var active_pane = $(this).attr('data-tab');
		$('#'+ active_pane ).show();

		return false;
	});


</script>

<script type="text/javascript">

 var _gaq = _gaq || [];
 _gaq.push(['_setAccount', 'UA-1429228-8']);
 _gaq.push(['_setDomainName', 'librivox.org']);
 _gaq.push(['_trackPageview']);

 (function() {
  var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
  ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
   var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

 </script>