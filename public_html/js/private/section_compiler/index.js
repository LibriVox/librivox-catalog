$(document).ready(function() {

	$('.icon-remove').tooltip();
	$('.icon-search').tooltip();

	bind_edit();


	

    $.extend( $.fn.dataTable.defaults, {
        "bFilter": false,
        "bPaginate" : false,
        "bInfo" : false,
    } );

	var oTable = $('#m_sections_table').dataTable({
		fnDrawCallback: function( oSettings ) {
	  		$('#sections_table td').addClass('edit');
		}
	});


	// Return a helper with preserved width of cells
	var fixHelper = function(e, ui) {
		ui.children().each(function() {
			$(this).width($(this).width());
		});
		return ui;
	};

	$("#m_sections_table tbody").sortable({
		helper: fixHelper,
		update: function(){
			order_sections();
			relabel_section_numbers();
		}
	});    // .disableSelection()


	//this may move if also used in validator..
	$('#status').on('change', function(){
		var status = $(this).val();
		var project_id = $('#project_id').val();

		var data = {'status': status, 'id': project_id};
		var response_obj = update_project(data);
	});

	function update_project(data_obj)
	{
		$.ajax({
	        url: CI_ROOT + "private/projects/update_project",
	        type: 'post',
	        data: data_obj,
	        complete: function(r){
	          //return jQuery.parseJSON(r.responseText);
	          window.location.reload(); //as always...now this will trigger visibility of certain fields, so need a referesh
	        }
	    });		
	}



	//display & manage meta-data about rows
	$('.meta_data').live('click', function(){

		var section_id 		= $(this).attr('data-section_id');
		$('.add_btn').hide();

		$.ajax({
	        url: CI_ROOT + "private/section_compiler/get_meta_data",
	        type: 'post',
	        data: {'section_id': section_id },
	        complete: function(r){
	          var response_obj = jQuery.parseJSON(r.responseText);
	          
	          if (response_obj.status == 'FAIL')
	          {
	              set_message(response_obj.data.message, 'error');
	          }
	          else
	          {

	          	var section = response_obj.data.section;
	          	$('#add_to_section_id').val(section_id);

				$('#add_to_section_number').val(section.section_number).attr('disabled', 'disabled');
				$('#add_author').val(section.author_name);
				$('#add_author').attr('data-add_author_id', section.author_id);
				$('#add_source').val(section.source);
				$('#add_playtime').val(section.playtime);
				$('#add_language_id').val(section.language_id); 
				$('#add_mp3_64_url').val(section.mp3_64_url); 
				$('#add_mp3_128_url').val(section.mp3_128_url); 

				$('#add_section_info_div' ).show(300);	          	
	          }	
	    	},
	    
		});

	});


 

	//add new section
	$('#add_section_btn').on('click', function(){
		var project_id = $('#project_id').val();

		var title 	= $('#add_section_title').val();
		var reader_id 	= $('#add_section_reader').attr('data-assign_reader_id'); 
		var notes 	= $('#add_section_notes').val();

		$.ajax({
	        url: CI_ROOT + "private/section_compiler/add_section",
	        type: 'post',
	        data: {'title': title ,'notes': notes ,'project_id': project_id, 'reader_id': reader_id},
	        complete: function(r){
				var response_obj = jQuery.parseJSON(r.responseText);

				if (response_obj.status == 'FAIL')
				{
				  	set_message(response_obj.data.message, 'error');
				}
				else
				{
					//reset
					$('#add_section_title').val('');
					$('#add_section_reader').val(''); 
					$('#add_section_notes').val('');

					//wimping out for the moment
					location.reload(true);

					/*
					var html = '<tr id="'+response_obj.data.id+'">'
					+ '<td class="section_number">'+pad2(response_obj.data.section_number)+'</td>'
					+ '<td><i class="icon-search meta_data" data-section_id="'+response_obj.data.id+'"></i></td>'
					+ '<td id="title-'+response_obj.data.id+'" class="edit">'+title+'</td>'
					+ '<td id="reader-'+response_obj.data.id+'" class="edit">'+reader+'</td>'
					+ '<td id="notes-'+response_obj.data.id+'" class="edit">'+notes+'</td>'
					+ '<td id="listen_url-'+response_obj.data.id+'" class="edit"></td>'
					+ '<td id="status-'+response_obj.data.id+'" class="edit"></td>'
					+'</tr>';
					$('#sections_table').append(html);
					*/

					oTable.fnAddData( [
						'<td class="section_number">'+pad2(response_obj.data.section_number)+'</td>',
						'<td><i class="icon-search meta_data" data-section_id="'+response_obj.data.id+'"></i></td>',
						'<td id="title-'+response_obj.data.id+'" class="edit">'+title+'</td>',
						'<td id="reader-'+response_obj.data.id+'" class="edit">'+reader+'</td>',
						'<td id="notes-'+response_obj.data.id+'" class="edit">'+notes+'</td>',
						'<td id="listen_url-'+response_obj.data.id+'" class="edit">http://librivox.local/uploads/</td>',
						'<td id="listen_link_url-'+response_obj.data.id+'"  style="cursor:default;"></td>',
						'<td id="status-'+response_obj.data.id+'" class="edit"></td>',
						'<td><i data-section_id="'+response_obj.data.id+'" data-placement="left" rel="tooltip" class="icon-remove delete_section" data-original-title="Remove Section"></i></td>'
						
					], true);

					bind_edit();

					set_message(response_obj.data.message, 'success');



					$('#add_section_div').hide();
					redisplay_buttons();
				}	
	    	},
	    
		});

	});

	$('.delete_section').live('click', function(){

		var btn = $(this);
		var section_id 	= btn.attr('data-section_id');

		$.ajax({
	        url: CI_ROOT + "private/section_compiler/delete_section",
	        type: 'post',
	        data: {'section_id': section_id},
	        complete: function(r){
	        	var deleted_row = btn.closest('tr').get(0);

	  			oTable.fnDeleteRow(
					oTable.fnGetPosition(
						deleted_row
					)
				);	
	        }	
	    });

	});

	//add section info (using row's meta icon button)
	$('#add_section_info_btn').on('click', function(){
		var section_id 		= $('#add_to_section_id').val();

		var author_id 		= $('#add_author').attr('data-add_author_id');  
		var source 			= $('#add_source').val(); 
		var language_id 	= $('#add_language_id option:selected').val();
		var playtime 		= $('#add_playtime').val(); 
		var mp3_64_url 		= $('#add_mp3_64_url').val();
		var mp3_128_url 	= $('#add_mp3_128_url').val();


		$.ajax({
	        url: CI_ROOT + "private/section_compiler/add_meta_data",
	        type: 'post',
	        data: {'id': section_id, 'author_id': author_id ,'source': source ,'language_id': language_id ,'playtime': playtime, 'mp3_64_url': mp3_64_url, 'mp3_128_url': mp3_128_url},
	        complete: function(r){
				var response_obj = jQuery.parseJSON(r.responseText);

				if (response_obj.status == 'FAIL')
				{
				  	set_message(response_obj.data.message, 'error');
				}
				else
				{
					//reset
					$('#add_author').val('');
					$('#add_author').attr('data-add_author_id', 0);
					$('#add_language_id').val(1); 
					$('#add_playtime').val('');
					$('#add_source').val('');
					
					$('#add_section_info_div').hide();
					redisplay_buttons();
				}	
	    	},
	    
		});

	});


	$('#assign_reader_toggle').on('click', function(){
		//reset
		$('#assign_reader').val('');
		$('#assign_reader').attr( 'data-assign_reader_id', 0);
		$('#assign_section').val('');
	});

	//add readers to sections
	$('#assign_reader_btn').on('click', function(){
		var assign_section = $('#assign_section').val();  
		var assign_section_array = parse_list_to_array(assign_section);

		var project_id = $('#project_id').val();

		var by_reader_id = 0;

		//two ways to enter - by dropdown, or ids list
		var reader_name = $('#assign_reader').val();
		var reader_id = $('#assign_reader').attr( 'data-assign_reader_id');

		//when we select a reader from dropdown, it sets the reader_id; otherwise, its default is zero
		if (reader_id == 0 && reader_name.length > 0)
		{
			reader_id = JSON.stringify(parse_list_to_array(reader_name));
			by_reader_id = 1;

		}	
		else
		{
			if (isNaN(parseInt(reader_id))) {
				alert('In order to add a reader, the reader information must be read from the dropdown. If you cannot find this reader name, please add it first using the "Add New Reader" button');
				return;
			};

			// make the id an array element here to stay consistent for backend
			reader_id = JSON.stringify(parse_list_to_array(reader_id));

		}	

		$.ajax({
	        url: CI_ROOT + "private/section_compiler/add_reader_sections",
	        type: 'post',
	        data: {'project_id': project_id,'reader_id': reader_id, 'section_list': JSON.stringify(assign_section_array) },
	        complete: function(r){
				var response_obj = jQuery.parseJSON(r.responseText);

				if (response_obj.status == 'FAIL')
				{
				  	set_message(response_obj.data.message, 'error');
				}
				else
				{
					if (by_reader_id ==1)
					{
						window.location.reload(); 
					}	

					//label the readers in the table
					set_reader_names(reader_id, reader_name, assign_section_array);

					//reset
					$('#assign_reader').val('');
					$('#assign_section').val('');
					$('#assign_reader_div').hide();
					redisplay_buttons();
				}	
	    	},
	    
		});		

	});	

	//add readers to sections
	$('#remove_reader_btn').on('click', function(){
		var assign_section = $('#assign_section').val();  
		var assign_section_array = parse_list_to_array(assign_section);

		var project_id = $('#project_id').val();
		var reader_id = $('#assign_reader').attr( 'data-assign_reader_id');
		var reader_name = $('#assign_reader').val();

		if (isNaN(parseInt(reader_id))) {
			alert('In order to remove a reader, the reader information must be read from the dropdown.');
			return;
		};

		$.ajax({
	        url: CI_ROOT + "private/section_compiler/remove_reader_sections",
	        type: 'post',
	        data: {'project_id': project_id,'reader_id': reader_id, 'section_list': JSON.stringify(assign_section_array) },
	        complete: function(r){
				var response_obj = jQuery.parseJSON(r.responseText);

				if (response_obj.status == 'FAIL')
				{
				  	set_message(response_obj.data.message, 'error');
				}
				else
				{
					window.location.reload();
				}	
	    	},
	    
		});		

	});	

	$('.add_btn').on('click', function(){
		$('.add_btn').hide();
		$('#' + $(this).attr('data-toggle_div')).toggle(300);
	});

	//cancel 
	$('.cancel_btn').on('click', function(){
		$('.toggle_div').hide(300);
		$('.add_btn').show(300);
	});


});


function order_sections(){

	var sortOrder = $('#m_sections_table tbody').sortable('toArray');
	sortOrder = JSON.stringify(sortOrder);  

	var project_id = $('#project_id').val();
	
	$.ajax({
        url: CI_ROOT + "private/section_compiler/order_sections",
        type: 'post',
        data: {'sortOrder': sortOrder ,'project_id': project_id},
        complete: function(r){
          var response_obj = jQuery.parseJSON(r.responseText);
          
          if (response_obj.status == 'FAIL')
          {
              $('#error_message').html(response_obj.data.message);
          }
    	},
    
	});

}



function bind_edit()
{
	var full_id;

	// editable fields
	
	$('.edit').editable(CI_ROOT + "private/section_compiler/update_section_value",
		{
			indicator : 'Saving...',
         	tooltip   : 'Click to edit...',
         	placeholder: '',
         	event: 'click',
         	select : true,
         	submitdata: function (value, settings) {
				//console.log(this);
				//console.log(value);
				//console.log(settings);
				full_id = this.id;

				//$('#listen_link_url-'+split_id[1]).html(this);
			},
			callback : function(r) {
				var split_id = full_id.split('-');
				if (split_id[0] == 'listen_url')
				{
					var html = '<a href="'+r+'">Link</a>';
					$('#listen_link_url-'+ split_id[1]).html(html);
				}	
			},

		}
	);


	
	$('.edit_area').editable(CI_ROOT + "private/section_compiler/update_section_value",
		{
			type      : 'textarea',
			rows	  :	5,
			cols      : 200,
			indicator : 'Saving...',
         	tooltip   : 'Click to edit...',
         	placeholder: '',
         	event: 'click',
         	select : true,
         	submit : 'Save'
		}
	);
	

	

	 $('.edit_status').editable(CI_ROOT + "private/section_compiler/update_section_value", 
	 { 
	    data   : " {'Open':'Open','Assigned':'Assigned', 'Ready for PL':'Ready for PL','See PL notes':'See PL notes','Ready for spot PL':'Ready for spot PL','PL OK':'PL OK','selected':'Open'}",
	    placeholder: '',
	    type   : 'select',
	    //submit : 'OK', -- remove this and submit via code below
		callback : function(r) {
		},
	 });

	 // will submit generated jedit  form on change rather than with submit button
    $('select').live('change', function () {
         $(this).parent().submit();
    });


}


function relabel_section_numbers()
{
	first_section = Math.abs(parseInt($('#has_preface').val()) - 1);

	$('.section_number').each(function(index, value){
		$(this).html(pad2(index + first_section));
		$(this).parent('tr').attr('data-section_number', index + first_section);
	});
}

function pad2(number) {   
     return (number < 10 ? '0' : '') + number   
}

function redisplay_buttons()
{
	$('#add_section_toggle').show();
	$('#add_reader_toggle').show();
	$('#assign_reader_toggle').show();
}



function parse_list_to_array(section_numbers)
{
	var sections = [];

	$.each(section_numbers.split(','), function(index, value){
		if (value.indexOf('-') > 0)
		{
			var range_endpoints = value.split('-');
			sections = sections.concat(range(parseInt(range_endpoints[0]), parseInt(range_endpoints[1])));
		}
		else
		{
			sections.push(parseInt(value));
		}				
	});
	return sections;
}

function range(start, stop){
    var result = [];
    for (var i=start; i <= stop; i++){
        result.push(i);
    };
    return result;
};


function set_reader_names(reader_id, reader_name,assign_section_array)
{

	var person_url = PEOPLE_LINK +reader_id;
	var person_link = '<a href="'+person_url+'">'+reader_name+'</a> ';

	$.each(assign_section_array, function(index, value){
		var curr_html = $('tr[data-section_number="'+value+'"]').children('.reader').html();
		$('tr[data-section_number="'+value+'"]').children('.reader').html(curr_html + person_link);

		//we also need to move any "Open" to "Assigned", but not other statuses
		var status = $('tr[data-section_number="'+value+'"]').children('.edit_status').html();  
		if (status == 'Open')
		{
			$('tr[data-section_number="'+value+'"]').children('.edit_status').html('Assigned');
		}	

	});
}

function assign_vars(item)
{
	var name_field;
	if (item.username == undefined) 
	{
		name_field = item.first_name + ' ' + item.last_name;
	}	
	else
	{
		name_field = item.username;
	}	

    return {
        label: name_field,
        value: name_field,
        source_id: item.id,     
        source_name: name_field,                             
    }
}

function assign_elements(search_area, ui, array_index)
{
	//array_index unused - just a generic part of the add author & translator code from other screens

	switch (search_area)
	{
		case 'reader':
			$('#assign_reader').val(ui.item.source_name);
    		$('#assign_reader').attr( 'data-assign_reader_id' ,ui.item.source_id);
    		break;
		case 'section_reader':
			$('#add_section_reader').val(ui.item.source_name);
    		$('#add_section_reader').attr( 'data-assign_reader_id' ,ui.item.source_id);
    		break;
		case 'author':
			$('#add_author').val(ui.item.source_name);
    		$('#add_author').attr( 'data-add_author_id' ,ui.item.source_id);
    		break;
	}

}