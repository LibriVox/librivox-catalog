$(document).ready(function() {

	var project_id = $('#project_id').val();
	if (project_id > 0) ajax_search_catalog(project_id);

	var keywords_from_db = $('#keywords_from_db').val();

    $('#list_keywords').tagsInput({
       'height':'100px',
       'width':'770px',
    }).importTags(keywords_from_db);


	$('#author_blocks').html('');
	$('#translator_blocks').html('');

	$('.toggle_form_btn[data-toggle_div_id="add_project_form"]').live('click', function(){
		$('#search_projects_form').hide();
	});

	$('.toggle_form_btn[data-toggle_div_id="search_projects_form"]').live('click', function(){
		$('#add_project_form').hide();
	});



    $( "#begindate" ).datepicker({dateFormat: 'yy-mm-dd'});
    $( "#targetdate" ).datepicker({dateFormat: 'yy-mm-dd'});
    $( "#catalogdate" ).datepicker({dateFormat: 'yy-mm-dd'});

    if ($( "#catalogdate" ).val() == '0000-00-00') $( "#catalogdate" ).val(''); //show it as empty	

	$('#status').live('change', function(){
		var today = moment().format("YYYY-MM-DD");

		if ($(this).val() == 'complete') $( "#catalogdate" ).val(today); 
		else $( "#catalogdate" ).val('');
	});	
});

// populates New project form from Project Lookup Code form
$('#add_project_form_btn').live('click', function(){

	var this_btn = $(this);
	
	$.ajax({
		  url: CI_ROOT + 'private/administer_projects/ajax_lookup_project_code',
		  type: 'post',
		  data: $(this).closest('form').serialize(),
	      complete: function(r){
			var response_obj = jQuery.parseJSON(r.responseText);

				if (response_obj.status == 'SUCCESS')
				{
					this_btn.closest('.toggle_div').hide();

					$('#results').html('');

					populate_project_form(response_obj.data);

					populate_authors(response_obj.data);

					populate_translators(response_obj.data);

					set_autocomplete();

					populate_genres(response_obj.data)

					//build_authors(response_obj.data.authors);
					$('#project_form').show();
				}
				else
				{
					$('#project_form').hide();
					$('#results').html('No results found');

				}	
		  },
	}); 
});

//pouplates form from db based on clicked result item
$('.result_item').live('click', function(){
		var project_id =  $(this).attr('data-id');
		ajax_search_catalog(project_id);
});


function ajax_search_catalog(project_id)
{
	$.ajax({
		  url: CI_ROOT + 'private/projects/ajax_search_catalog',
		  type: 'post',
		  data: { 'projectid' : project_id},
	      complete: function(r){
			var response_obj = jQuery.parseJSON(r.responseText);

				if (response_obj.status == 'SUCCESS')
				{
					add_volunteers_to_dropdowns(response_obj.data[0]);

					populate_form_from_project(response_obj.data[0]);
					$('#project_form').show();
					$(document).scrollTop( $("#project_form").offset().top );
				}
				else
				{
					$('#project_form').hide();
					$('#results').html('No results found');

				}	
		  },
	});
}




//save or update project
$('#add_catalog_item_form_submit').live('click', function(){
	$('.valid').removeClass("form_error");

	$.ajax({
		  url: CI_ROOT + 'private/administer_projects/ajax_add_catalog_item',
		  type: 'post',
		  data: $(this).closest('form').serialize(),
	      complete: function(r){
	      	
			var response_obj = jQuery.parseJSON(r.responseText);
				if (response_obj.status == 'SUCCESS')
				{
					$('#showErrors').html('');
					$('#showErrors').hide();

					$('#project_id').val(response_obj.data.project_id);

					$('#project_form form #section_compiler_url').attr('href', CI_ROOT+'section_compiler/'+ response_obj.data.project_id).show();
					$('#project_form form #validator_url').attr('href', CI_ROOT+'validator/'+ response_obj.data.project_id).show();		

					//alert(response_obj.data.url_librivox);
					$('#project_form form #librivoxurl').val(response_obj.data.url_librivox);
					$('#project_form form #librivoxurl_link').attr('href', response_obj.data.url_librivox);			

					if (response_obj.data.new_authors.length > 0)
					{
						match_new_authors(response_obj.data.new_authors);
					}	

					$(document).scrollTop( $("#project_form").offset().top );
					$('#message').show().fadeOut(2000);
 
				}
				else
				{
					$('#showErrors').html(response_obj.data.msg);
					$('#showErrors').show();
					$(document).scrollTop( $("#project_form").offset().top );

				}	
				
		  },
	});		
});

function populate_project_form(data)
{
	$.each(data, function(index, value) { 
	  $('#project_form form #'+index).val(value); 
	});

	//language dropdown
	$('#project_form form #recorded_language').val(data.recorded_language);

	//checkboxes
	var is_compilation = ( data.is_compilation == 1)? true: false;
	$('#project_form form #is_compilation').attr('checked', is_compilation);

	//$('#project_form form #'+index).val(value);

	$('#project_form form #keywords_from_db').val(data.list_keywords); //assign to hidden
	$('#list_keywords').importTags(data.list_keywords);

	return false;		
}


function populate_authors(data)
{
	if (!data.hasOwnProperty('author_views')) return false;

	$('#author_blocks').html('');
	$.each(data.author_views, function(array_index, value) { 
	  	$('#author_blocks').append(value); 

	  	//why? just makes life easier to not have to pass data into the partial view directly
	  	// unfortunately, "value" is the one attribute you can't set dynamically & then access, and we need
	  	// to search on first & last name to find new authors with no auth id
	    $('input[id^="auth_first_name"][data-array_index="'+array_index+'"]').val(data.authors[array_index].first_name);    
	    $('input[id^="auth_first_name"][data-array_index="'+array_index+'"]').attr('temp_val',data.authors[array_index].first_name);
	    
	    $('input[id^="auth_last_name"][data-array_index="'+array_index+'"]').val(data.authors[array_index].last_name);
	    $('input[id^="auth_last_name"][data-array_index="'+array_index+'"]').attr('temp_val',data.authors[array_index].last_name);

	    $('input[id^="auth_yob"][data-array_index="'+array_index+'"]').val(data.authors[array_index].dob);
	    $('input[id^="auth_yod"][data-array_index="'+array_index+'"]').val(data.authors[array_index].dod);
	    $('input[id^="link_to_auth"][data-array_index="'+array_index+'"]').val(data.authors[array_index].author_url);

	    $('a[id^="link_to_auth_link"][data-array_index="'+array_index+'"]').attr('href', data.authors[array_index].author_url);

	    $('input[id^="auth_id"][data-array_index="'+array_index+'"]').val(data.authors[array_index].id);
	    $('input[id^="auth_id"][data-array_index="'+array_index+'"]').attr('temp_val', data.authors[array_index].id);


	    if (data.authors[array_index].hasOwnProperty("suggestion"))
	    {
	    	$('#author_block_'+array_index+ ' hr' ).before('<div style="font-weight: bold;margin-left:20px !important;">Author ' + data.authors[array_index].first_name + ' ' + data.authors[array_index].last_name + ' is not in the database. Suggestions include:</div>');
		    $.each(data.authors[array_index].suggestion, function(suggestion_index, suggestion_value) {
	    	
		    	var suggestion = format_suggestion(array_index, suggestion_value); 
		    	$('#author_block_'+array_index + ' hr' ).before(suggestion);


		    });		    	
	    }	

	    $("#add_author").attr('data-counter', array_index);

	});
	return false;

}

function populate_translators(data)
{
	if (!data.hasOwnProperty('translator_views')) return false;

	$('#translator_blocks').html('');
	$.each(data.translator_views, function(array_index, value) { 
	  	$('#translator_blocks').append(value); 

	  	//why? just makes life easier to not have to pass data into the partial view directly
	  	// unfortunately, "value" is the one attribute you can't set dynamically & then access, and we need
	  	// to search on first & last name to find new authors with no auth id
	    $('input[id^="trans_first_name"][data-array_index="'+array_index+'"]').val(data.translators[array_index].first_name);    
	    $('input[id^="trans_first_name"][data-array_index="'+array_index+'"]').attr('temp_val',data.translators[array_index].first_name);
	    
	    $('input[id^="trans_last_name"][data-array_index="'+array_index+'"]').val(data.translators[array_index].last_name);
	    $('input[id^="trans_last_name"][data-array_index="'+array_index+'"]').attr('temp_val',data.translators[array_index].last_name);

	    $('input[id^="trans_yob"][data-array_index="'+array_index+'"]').val(data.translators[array_index].dob);
	    $('input[id^="trans_yod"][data-array_index="'+array_index+'"]').val(data.translators[array_index].dod);
	    $('input[id^="link_to_trans"][data-array_index="'+array_index+'"]').val(data.translators[array_index].author_url);

	    $('a[id^="link_to_trans_link"][data-array_index="'+array_index+'"]').attr('href', data.translators[array_index].author_url);

	    $('input[id^="trans_id"][data-array_index="'+array_index+'"]').val(data.translators[array_index].id);
	    $('input[id^="trans_id"][data-array_index="'+array_index+'"]').attr('temp_val', data.translators[array_index].id);


	    if (data.translators[array_index].hasOwnProperty("suggestion"))
	    {
	    	$('#translator_block_'+array_index+ ' hr' ).before('<div style="font-weight: bold;margin-left:20px !important;">Translator ' + data.translators[array_index].first_name + ' ' + data.translators[array_index].last_name + ' is not in the database. Suggestions include:</div>');
		    $.each(data.translators[array_index].suggestion, function(suggestion_index, suggestion_value) {
	    	
		    	var suggestion = format_suggestion(array_index, suggestion_value); 
		    	$('#translator_block_'+array_index + ' hr' ).before(suggestion);
		    });		    	
	    }	

	    $("#add_translator").attr('data-counter', array_index);

	});
	return false;

}

function populate_genres(data)
{
	if (!data.hasOwnProperty('genre_strings')) return false;

	var genre_tags ='';
	$.each(data.genre_strings, function(array_index, value) {
		genre_tags = genre_tags + value;
	});

	$('#genres_div').html(genre_tags);
}

function match_new_authors(new_authors)
{
	//clutzy, but we need to search through the page of authors & match the new author so we can update the auth_id
	$.each(new_authors, function(array_index, value) {
		var last_name_index = $('input[temp_val="'+value.last_name+'"]').attr('data-array_index');
		var first_name_index = $('input[temp_val="'+value.first_name+'"]').attr('data-array_index');

		if(last_name_index == first_name_index)
		{
			$('input[id^="auth_id"][data-array_index="'+last_name_index+'"]').val(value.auth_id);
	    	$('input[id^="auth_id"][data-array_index="'+last_name_index+'"]').attr('temp_val', value.auth_id);			
		}	
	});
}

function format_suggestion(array_index, suggestion_value)
{
	var html = '<div class="author_suggestion alert alert-error span9" style="margin:6px 0 6px 30px !important;cursor:pointer;" '; 
	html = html + ' data-array_index = "' + array_index + '"' ;
	html = html + ' data-auth_id = "' + suggestion_value.id + '"' ;
	html = html + ' data-auth_first_name = "' + suggestion_value.first_name + '"';
	html = html + ' data-auth_last_name = "' + suggestion_value.last_name + '"';
	html = html + ' data-auth_dob = "' + suggestion_value.dob + '"';
	html = html + ' data-auth_dod = "' + suggestion_value.dod + '"';
	html = html + ' data-auth_author_url = "' + suggestion_value.author_url + '" >';
	//html = html + '<button type="button" class="close" data-dismiss="alert alert-info">×</button>';
	html = html + suggestion_value.first_name + ' ' + suggestion_value.last_name  + ' (' + suggestion_value.dob + ' - ' + suggestion_value.dod + ')' + '</div>';
	return html;
}

// when user clicks on suggestion, replaces all values
$('.author_suggestion').live('click', function(){
	var array_index = $(this).attr('data-array_index');

    $('input[id^="auth_first_name"][data-array_index="'+array_index+'"]').val($(this).attr('data-auth_first_name'));  
    $('input[id^="auth_last_name"][data-array_index="'+array_index+'"]').val($(this).attr('data-auth_last_name'));
    $('input[id^="auth_yob"][data-array_index="'+array_index+'"]').val($(this).attr('data-auth_dob'));
    $('input[id^="auth_yod"][data-array_index="'+array_index+'"]').val($(this).attr('data-auth_dod'));
    $('input[id^="link_to_auth"][data-array_index="'+array_index+'"]').val(decodeURIComponent($(this).attr('data-auth_author_url')));
    $('input[id^="auth_id"][data-array_index="'+array_index+'"]').val($(this).attr('data-auth_id'));

});



function populate_form_from_project(data)
{
	//our new fields don't match the old :-( so we have to map them
	$('#project_form form #project_id').val(data.id);


	$('#project_form form #section_compiler_url').attr('href', CI_ROOT+'section_compiler/'+ data.id).show();
	$('#project_form form #validator_url').attr('href', CI_ROOT+'validator/'+ data.id).show();

	$('#project_form form #projectname').val(data.title);
	$('#project_form form #title_prefix').val(data.title_prefix);
	$('#project_form form #projectdescription').val(data.description);
	$('#project_form form #copyrightyear').val(data.copyright_year);
	$('#project_form form #project_type').val(data.project_type);

		//language dropdown
	$('#project_form form #recorded_language').val(data.language_id);

	
	var checked = ( data.copyright_check == 1)? true: false;
	$('#project_form form #copyrightcheck').attr('checked', checked);

	$('#project_form form #nsections').val(data.num_sections);

	var has_preface = ( data.has_preface == 1)? 0: 1;
	$('#project_form form #firstsection').val(has_preface);

	$('#project_form form #totaltime').val(data.totaltime);
	$('#project_form form #zip_size').val(data.zip_size);


	var is_compilation = ( data.is_compilation == 1)? true: false;
	$('#project_form form #is_compilation').attr('checked', is_compilation);	

	$('#project_form form #begindate').val(data.date_begin);
	$('#project_form form #targetdate').val(data.date_target);

	$('#project_form form #status').val(data.status);
	$('#project_form form #catalogdate').val(data.date_catalog);
	$('#project_form form #language').val(data.language_id);

	$('#project_form form #genres').val(data.genres);
	populate_genres(data);

	$('#project_form form #notes').val(data.notes);

	$('#project_form form #coverart_pdf').val(data.coverart_pdf);
	$('#project_form form #coverart_jpg').val(data.coverart_jpg);
	$('#project_form form #coverart_thumbnail').val(data.coverart_thumbnail);

	$('#project_form form #keywords_from_db').val(data.list_keywords); //assign to hidden
	$('#list_keywords').importTags(data.list_keywords);

	//volunteers
	$('#project_form form #person_bc_id').val(data.person_bc_id);
	$('#project_form form #person_altbc_id').val(data.person_altbc_id);
	$('#project_form form #person_mc_id').val(data.person_mc_id);
	$('#project_form form #person_pl_id').val(data.person_pl_id);	

	//urls
	$('#project_form form #librivoxurl').val(data.url_librivox);
	$('#project_form form #forumurl').val(data.url_forum);
	$('#project_form form #archiveorgurl').val(data.url_iarchive);
	$('#project_form form #gutenburgurl').val(data.url_text_source);
	$('#project_form form #wikibookurl').val(data.url_project);
	$('#project_form form #zip_url').val(data.zip_url);

	//links
	$('#project_form form #librivoxurl_link').attr('href',data.url_librivox);
	$('#project_form form #forumurl_link').attr('href',data.url_forum);
	$('#project_form form #archiveorgurl_link').attr('href',data.url_iarchive);
	$('#project_form form #gutenburgurl_link').attr('href',data.url_text_source);
	$('#project_form form #wikibookurl_link').attr('href',data.url_project);
	$('#project_form form #zip_url_link').attr('href',data.zip_url);


	//authors
	populate_authors(data);

	//translators
	populate_translators(data);

	return false;		
}

//should move to common js file
$("#add_author").on('click', function(){
    var counter = $(this).attr('data-counter');   

    $.ajax({
        url: CI_ROOT + 'public/public_ajax/add_author',
        type: 'post',
        async:   false, 
        data: {"counter": counter },
        complete: function(r){
            var response_obj = jQuery.parseJSON(r.responseText);

            $('#author_blocks').append(response_obj.html);
            set_autocomplete();
            $("#add_author").attr('data-counter', parseInt(counter) +1);
        },

    });

});

//should move to common js file
$("#add_translator").on('click', function(){
    var counter = $(this).attr('data-counter');   

    $.ajax({
        url: CI_ROOT + 'public/public_ajax/add_translator',
        type: 'post',
        async:   false, 
        data: {"counter": counter },
        complete: function(r){
            var response_obj = jQuery.parseJSON(r.responseText);

            $('#translator_blocks').append(response_obj.html);
            set_autocomplete();
            $("#add_translator").attr('data-counter', parseInt(counter) +1);
        },

    });

});


function assign_vars(item)
{
	if (!item.dob) {item.dob = '';}
	if (!item.dod) {item.dod = '';}

	var lifespan = '';
	if (item.dob || item.dod){lifespan = '  (' + item.dob + ' - ' + item.dod + ')';}

    return {
        label: item.first_name + ' ' + item.last_name + lifespan,
        value: item.first_name,
        first_name: item.first_name,
        last_name: item.last_name,
        dob: item.dob,
        dod: item.dod, 
        author_url: item.author_url, 
        author_id: item.id,                                   
    }
}

function assign_elements(search_area, ui, array_index)
{
	switch (search_area)
	{
		case 'author':

		    $('input[id^="auth_first_name"][data-array_index="'+array_index+'"]').val(ui.item.first_name);
	        $('input[id^="auth_last_name"][data-array_index="'+array_index+'"]').val(ui.item.last_name);
            $('input[id^="auth_yob"][data-array_index="'+array_index+'"]').val(ui.item.dob);
            $('input[id^="auth_yod"][data-array_index="'+array_index+'"]').val(ui.item.dod);
            $('input[id^="link_to_auth"][data-array_index="'+array_index+'"]').val(ui.item.author_url);
            $('input[id^="auth_id"][data-array_index="'+array_index+'"]').val(ui.item.author_id);
            $('a[id^="link_to_auth_link"][data-array_index="'+array_index+'"]').attr('href', ui.item.author_url);    
	        break;
	    case 'translator':
            $('input[id^="trans_first_name"][data-array_index="'+array_index+'"]').val(ui.item.first_name);
            $('input[id^="trans_last_name"][data-array_index="'+array_index+'"]').val(ui.item.last_name);
            $('input[id^="trans_yob"][data-array_index="'+array_index+'"]').val(ui.item.dob);
            $('input[id^="trans_yod"][data-array_index="'+array_index+'"]').val(ui.item.dod);
            $('input[id^="link_to_trans"][data-array_index="'+array_index+'"]').val(ui.item.author_url);
            $('input[id^="trans_id"][data-array_index="'+array_index+'"]').val(ui.item.author_id);
            $('a[id^="link_to_trans_link"][data-array_index="'+array_index+'"]').attr('href', ui.item.author_url);    

	        break;
    }
}

$('#btn_project_readers_modal').on('click', function(){
	var project_id = $('#project_id').val();

	if (project_id == 0)
	{
		alert('Please save the project so that it has a Project Id number before adding these urls');
		return false;
	} 


    $.ajax({
          url: CI_ROOT + 'private/administer_projects/ajax_get_reader_list',
          type: 'post',
          data: {'project_id' : project_id },
          complete: function(r){
            var response_obj = jQuery.parseJSON(r.responseText);

            
            var sections = response_obj.data.sections;
            var html = _sections_build_table(sections);

            $('#project_readers_list').html(html);
            $('#project_readers_modal').modal();
 
          },
    }); 

});

function _sections_build_table(sections)
{
    var html = '<table>'; 
    html = html + '<tr><th style="text-align:left;width:10%;">Section</th><th style="text-align:left;width:40%;">Title</th><th style="text-align:left;width:25%;">Reader</th><th style="text-align:left;width:25%;">Display name</th></tr>';

    jQuery.each(sections, function(index, value){
        html = html + _sections_row_template(value);
    });  

    html = html + '<table>'; 
    return html;      
}

function _sections_row_template(value)
{
    var html = '<tr>';
    html = html + '<td>'+ value.section_number +'</td>'
    html = html + '<td>'+ value.title +'</td>'
    html = html + '<td><a href="'+ CI_ROOT +'reader/' +value.reader_id+ '">'+ value.reader_name +'</a></td>'
    html = html + '<td>'+ value.display_name +'</td>'        
    html = html + '<tr>';

    return html;
}



$('#btn_project_urls_modal').on('click', function(){
	var project_id = $('#project_id').val();

	if (project_id == 0)
	{
		alert('Please save the project so that it has a Project Id number before adding these urls');
		return false;
	} 

	//clear the add form
	$('#project_url_url_0').val('');
	$('#project_url_label_0').val('');
	$('#project_url_order_0').val('');

    $.ajax({
          url: CI_ROOT + 'private/administer_projects/get_project_urls',
          type: 'post',
          data: {'project_id' : project_id },
          complete: function(r){
            var response_obj = jQuery.parseJSON(r.responseText);

            
            var project_urls = response_obj.data.project_urls;
            var html = _project_urls_build_table(project_urls);

            $('#project_urls_list').html(html);
            $('#project_urls_modal').modal();
 
          },
    }); 	
	
});


function _project_urls_build_table(project_urls)
{
    var html = '<table>'; 
    jQuery.each(project_urls, function(index, value){
        html = html + _project_urls_row_template(value);
    });  

    html = html + '<table>'; 
    return html;      
}


function _project_urls_row_template(value)
{
    var html = '<tr>';
    html = html + '<td><input type="text" id="project_url_url_'+value.id+'" value="' + value.url + '" /></td>'
    + '<td><input type="text" id="project_url_label_'+value.id+'" value="' + value.label + '" /></td>'
    + '<td><input type="text" class="input-small" id="project_url_order_'+value.id+'" value="' + value.order + '" /></td>'
    + '<td style="vertical-align: top !important;"><input type="button" style="margin-left:6px;" class="submit_project_url btn" data-project_url_id="'+value.id+'" data-project_id="'+value.project_id+'" value="Save">'
    + '<input type="button" style="margin-left:6px;" class="remove_project_url btn" data-project_url_id="'+value.id+'"  value="Remove"></td>';
    html = html + '<tr>';

    return html;
}



$('.submit_project_url').live('click', function(){

    var id = $(this).attr('data-project_url_id');
    var project_id = $('#project_id').val();;
    var url = $('#project_url_url_'+ id).val();
    var label = $('#project_url_label_'+ id).val();
    var order = $('#project_url_order_'+ id).val();
    
    $.ajax({
          url: CI_ROOT + 'private/administer_projects/update_add_project_url',
          type: 'post',
          data: {'id' : id, 'project_id' : project_id, 'url': url, 'label': label, 'order': order },
          complete: function(r){
            var response_obj = jQuery.parseJSON(r.responseText);
 
 			if (response_obj.data.type == 'insert')
 			{
            	var html =  make_url_row(response_obj.data.project_url_id, url, label, order, project_id);
            	$('#project_urls_list').append(html); 				
 			}	


            //clear add form
            $('#project_url_url_0').val('');
            $('#project_url_label_0').val('');
            $('#project_url_order_0').val('');

          },
    });   

});

function make_url_row(id, url, label, order, project_id)
{
	return '<tr><td><input type="text" value="'+url+'" id="project_url_url_'+id+'"></td>'
	+ '<td><input type="text" value="'+label+'" id="project_url_label_'+id+'"></td>'
	+ '<td><input type="text" value="'+order+'" id="project_url_order_'+id+'" class="input-small"></td>'
	+ '<td style="vertical-align: top !important;"><input type="button" value="Save" data-project_id="' + project_id +'" data-project_url_id="'+id+'" class="submit_project_url btn" style="margin-left:6px;">'
	+ '<input type="button" value="Remove" data-project_url_id="'+id+'" class="remove_project_url btn" style="margin-left:6px;"></td></tr>';
}


$('.remove_project_url').live('click', function(){

    var id = $(this).attr('data-project_url_id');
    
    $.ajax({
          url: CI_ROOT + 'private/administer_projects/delete_project_url',
          type: 'post',
          data: {'id' : id },
          complete: function(r){
            var response_obj = jQuery.parseJSON(r.responseText);
            
            $('#project_urls_modal').modal('hide');
            alert(response_obj.data.message);
          },
    });   

});

