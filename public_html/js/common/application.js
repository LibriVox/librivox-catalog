
// ajax loader
$(document).ready(function(){
    $('#response_message')
    .hide()  // hide it initially
    .ajaxStart(function() {
        show_loader();
    })
    .ajaxStop(function() {       
        hide_loader();
    });


    function show_loader()
    {
        var $dialog = $('<div class="loading_img"></div>')
        .html('<img src="../../img/loading.gif"/>')
        .dialog({autoOpen: true,  modal: true});
        $('.ui-dialog-titlebar').remove();
        $('.loading_img').removeAttr("style");
    }

    function hide_loader()
    {
        $('.loading_img').remove();
    }


});

$(document).ajaxComplete(function myErrorHandler(event, xhr, ajaxOptions, thrownError) {
    //jeditable returns a string, not a proper response. we are just bypassing it so the loading & script will finish
    try {
        var response_obj = jQuery.parseJSON(xhr.responseText);
        //TODO: we shouldn't have all these exceptions - mostly from validator page, will look into
        if(response_obj !== null && response_obj.data !== undefined  && response_obj.data.code == 'not_logged_in') window.location = CI_ROOT;
    } catch (e) {
        return true;
    }    

});


// messaging
function set_message(element, message, addClass)
{
    // element ='response_message', message='An error occured', addClass='alert-error'
    element = typeof element !== 'undefined' ? element : 'response_message';
    message = typeof message !== 'undefined' ? message : 'An error occured';
    addClass = typeof addClass !== 'undefined' ? addClass : 'alert-error';

    if ($('#'+element).length > 0)
    {
        $('#'+element).html(message).removeClass().addClass('alert').addClass(addClass).show();
    }

}


//toggle forms
$('.toggle_form_btn').live('click', function(){
	var div = $(this).attr('data-toggle_div_id');
	$('#'+div).toggle();
}); 


// simple toggle
$('.toggle_button').live('click', function(){
	//clear the existing data, since we use this for edit as well
	$('#form').each (function(){
	  this.reset();
	});		
	if ($(this).val() == 'Cancel')
	{
		$(this).val('Add new');
	}
	else
	{
		$(this).val('Cancel');			
	}
	
	$('.toggle_div').toggle('slow');
	
});


//form filler for edit record - generic for any form
$('.edit_record').live('click', function(){
	var edit_row = jQuery.parseJSON($(this).attr('data-edit_row'));
	$.each(edit_row, function(index, value) { 
	  //alert(index + ': ' + value); 
	  $('#'+index).val(value); 
	});
	$('.toggle_button').val('Cancel');
	$('.toggle_div').show('slow');
	return false;
	
});


// genre tags code:
$('.genre_item').live('click', function(){

    var this_level = $(this).attr('data-level');
    var this_id = $(this).attr('data-id');

    this_id = build_id_list(this_id);
    $('#genres').val(this_id);

    var level1_id = $(this).closest('.level-1').children('a:first').attr('data-id');
    var level1_name = $(this).closest('.level-1').children('a:first').attr('data-name');

    var tag = level1_name;

    if (this_level > 1)
    {
        var level2_id = $(this).closest('.level-2').children('a:first').attr('data-id');
        var level2_name = $(this).closest('.level-2').children('a:first').attr('data-name');

        tag = level1_name+'/'+level2_name; //overwrite

        //id = build_id_list(level2_id);
        //$('#genres').val(id);       
    }
    
    if (this_level > 2)
    {
        var level3_id = $(this).closest('.level-3').children('a:first').attr('data-id');
        var level3_name = $(this).closest('.level-3').children('a:first').attr('data-name');

        tag = level1_name+'/'+level2_name+'/'+level3_name;  //overwrite

        //id = build_id_list(level3_id);
        //$('#genres').val(id);      
    }

    var this_id = $(this).attr('data-id');
    var found = genre_unique(this_id);
    if (found < 0)
    {
        tag = build_tag(this_id, tag);
        $('#genres_div').html($('#genres_div').html() + tag);             
    }    



});

$('.remove_genre_item').live('click', function(){
	var this_id = $(this).attr('data-id');
	var genre_ids = remove_genre_id(this_id);
	$('#genres').val(genre_ids);

	//remove tag element
	$(this).closest('.genre_tag').remove();
}); 

function build_tag(id, name)
{
    return '<span class="tag genre_tag" style="cursor:pointer;" data-id="'+id+'" data-name="'+name+'"><span>'+name+'&nbsp;&nbsp;</span><a class="remove_genre_item" title="Removing tag">x</a></span>';
}

function genre_unique(id)
{
    var retval = -1;
    $('.genre_tag').each(function(index, value){
        if(id == $(this).attr('data-id')){retval = 1;}
    });
    return retval;
}

function build_id_list(id)
{
	var genre_list = $('#genres').val();
	
	return (genre_list == '') ?  id : function(){
    	var genres = genre_list.split(',');
    	if (jQuery.inArray(id, genres) < 0)
    	{
    		genres.push(id);
    	}
		return genres.join();    		
	};

}

function remove_genre_id(id)
{
	var genre_list = $('#genres').val();
	var genres = genre_list.split(',');
	var remove_key = jQuery.inArray(id, genres);
	genres.splice(remove_key, 1);
	return genres.join();     	
}



//***  Search forms ***//

// lets us use the enter button
$('#search_projects_form').keypress(function (e) {  
  if (e.which == 13) {
    var this_btn = $('#search_catalog_form_btn');
    search_form_click(this_btn, this_btn.attr('data-page'));
  }
});

//searches projects 
$('#search_catalog_form_btn').on('click', function(){
    var this_btn = $(this);
    search_form_click(this_btn, this_btn.attr('data-page'));
});



//actual search logic - gets the project from db
function search_form_click(this_btn, page)
{
    
    $.ajax({
        url: CI_ROOT + 'private/projects/ajax_search_catalog',
        type: 'post',
        data: this_btn.closest('form').serialize(),
        complete: function(r){
        var response_obj = jQuery.parseJSON(r.responseText);

            if (response_obj.status == 'SUCCESS')
            {
                var html = '<h4>Results:</h4>';

                if(response_obj.data.length == 1)
                {
                    add_volunteers_to_dropdowns(response_obj.data[0]);

                    _single_result(response_obj.data[0], page);
                }
                else
                {
                    var html = create_result_lists(response_obj.data, page);
                    $('#results').html(html);
                    $('#results').show();
                    
                }                           
            }
            else
            {
                $('#project_form').hide();
                $('#results').html('No results found');
                $('#results').show();
                
            }
    
        }
    });
} 

function add_volunteers_to_dropdowns(data)
{
    //volunteers
    if (typeof data.volunteers.bc != 'undefined')
    {
        $('#project_form form #person_bc_id').append('<option value='+data.volunteers.bc.user_id+'>'+data.volunteers.bc.username+'</option>');
    }

    if (typeof data.volunteers.altbc != 'undefined')
    {
        $('#project_form form #person_altbc_id').append('<option value='+data.volunteers.altbc.user_id+'>'+data.volunteers.altbc.username+'</option>');
    }

    if (typeof data.volunteers.mc != 'undefined')
    {
        $('#project_form form #person_mc_id').append('<option value='+data.volunteers.mc.user_id+'>'+data.volunteers.mc.username+'</option>');
    }

    if (typeof data.volunteers.pl != 'undefined')
    {
        $('#project_form form #person_pl_id').append('<option value='+data.volunteers.pl.user_id+'>'+data.volunteers.pl.username+'</option>');
    }           
    
}


function _single_result(data, page)
{
    if (page == 'new_project_form')
    {
        populate_form_from_project(data);  //response_obj.data[0]
        $('#results').hide();
        $('#project_form').show();
        $(document).scrollTop( $("#project_form").offset().top );
    }
    else
    {
        window.location.href = CI_ROOT + page + '/' + data.id;
    } 

}

function create_result_lists(data, page)
{

    var html = '';

    var complete_array = jQuery.grep(data, function(val, ind){
      return (val.status == 'complete');
    });

    if (complete_array.length > 0)
    {
        html = html + _create_list_html('Complete' ,complete_array, page);
    }   

    var open_array = jQuery.grep(data, function(val, ind){
      return (val.status != 'complete');
    });

    if (open_array.length > 0)
    {
        html = html + _create_list_html('Open' ,open_array, page);
    }   

    return html;
}

function _create_list_html(title ,array, page)
{
    var html = '<h4>'+title+'</h3>';
    html = html + '<ul class="result_list">';
    $.each(array, function(index, value) { 
        status = (title.toLowerCase() == 'complete')? '' : ' ('+ value.status+')'
        if (page == 'new_project_form')
        {  
            html = html + '<li><div class="result_item" data-id="'+ value.id +'">'+ value.id + ' - ' + value.title + status.replace('_', ' ') +'</div></li>'; 
        }
        else
        {
            html = html + '<li><a class="result_item" href="'+ CI_ROOT + page + '/' + value.id +'">'+ value.id + ' - ' + value.title + status.replace('_', ' ') + '</a></li>'; 
        }
    });
    return html + '</ul>';
}  