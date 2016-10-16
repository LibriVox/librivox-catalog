
// Starting point - create our directory //

$('#create_dir_btn').on('click', function(){

	//reset error message
	set_message('','', '');

	var project_id = $('#project_id').val();
	var validator_dir = $('#validator_dir').val();

	if (!validate_dir_name(validator_dir))
	{
		set_message('response_message_validator','There is an error in your name. Please use only letters, numbers, underscores and hyphens', 'error');
		return false;
	}

	if (!save_validator_dir(project_id, validator_dir))  return false;

	$('#create_dir_div').hide();
	$('#validator').show();


});


// Copy our files //

$('#copy_project_files').on('click', function(){
	var project_id = $('#project_id').val();
	var copied = copy_project_files(project_id);

    if (copied) window.location.reload();

});

//*** Link a file - probably a new upload - to the meta data for this section ***//

// toggle the form //
$('.link_section').on('click', function(){
    var file_name = $(this).attr('data-file_name');
    var project_id = $('#project_id').val();

    $('#link_section_file_name').html(file_name);
    $('#link_section_number').attr('data-original_name', file_name);

    $('#link_section').toggle();

});    

// send the data //
$('#link_section_data').on('click', function()
{
    var project_id      = $('#project_id').val();
    var file_name       = $('#link_section_number').attr('data-original_name');
    var section_number  = $('#link_section_number').val();

    $.ajax({
        url: CI_ROOT + 'private/validator/link_section_data',
        type: 'post',
        data: {"project_id": project_id, 'file_name': file_name,  'section_number': section_number},
        complete: function(r){
            var response_obj = jQuery.parseJSON(r.responseText);
            if (response_obj.status == 'FAIL')
            {
                set_message('response_message_validator',response_obj.data.message, 'alert-error');
            }
            else
            {
                window.location.reload();
            }
        },

    });

});    


//*** Allows us to update a single file -- REWORK to ignore the section, just use id3tag info ***//

// toggle the form //
$('.meta_data').on('click', function(){
    var file_name = $(this).attr('data-file_name');
    var project_id = $('#project_id').val();

    $('#edit_file_name').val(file_name);
    $('#edit_file_name').attr('data-original_name', file_name);

    var section_data = get_file_tags(project_id, file_name);
    if (section_data != false)
    {
        $('#edit_chapter_name').val(section_data.section.title);
        $('#edit_album_name').val(section_data.section.album);
        $('#edit_artist_name').val(section_data.section.artist);
    }    
    $('#file_edit').toggle();
});

// send the data //
$('#update_meta_data').on('click', function()
{
    var project_id      = $('#project_id').val();
    var original_file   = $('#edit_file_name').attr('data-original_name');

    var file_name       = $('#edit_file_name').val();
    var title           = $('#edit_chapter_name').val();
    var album           = $('#edit_album_name').val();
    var artist          = $('#edit_artist_name').val();

    $.ajax({
        url: CI_ROOT + 'private/validator/update_metadata',
        type: 'post',
        data: {"project_id": project_id, 'original_file': original_file , 'file_name': file_name, 'title': title, 'album': album, 'artist': artist},
        complete: function(r){
            var response_obj = jQuery.parseJSON(r.responseText);
            if (response_obj.status == 'FAIL')
            {
                set_message('response_message_validator',response_obj.data.message, 'error');
            }
            else
            {
                window.location.reload();
            }
        },

    });

});

//*** Update meta data on ALL files ***//

// name //
$('#update_rename_files').on('click', function()
{
    var project_id      = $('#project_id').val();
    var action = 'update_name';
    var name_part_1 = $('#update_rename_files_1').val();
    var name_part_2 = $('#update_rename_files_2').val();

    var postdata = {'project_id': project_id,'action': action, 'name_part_1': name_part_1, 'name_part_2': name_part_2};
    ajax_send(postdata);
} );   
// album //
$('#update_set_album').on('click', function()
{
    var project_id      = $('#project_id').val();
    var action = 'update_tags';
    var album = $('#update_album').val();
    var postdata = {'project_id': project_id,'action': action, 'album': album};
    ajax_send(postdata);
}); 

// artist //
$('#update_set_artist').on('click', function()
{
    var project_id      = $('#project_id').val();
    var action = 'update_tags';
    var artist = $('#update_artist').val();
    var postdata = {'project_id': project_id,'action': action, 'artist': artist};
    ajax_send(postdata);    
} );

$('#reset_track_numbers').on('click', function()
{
    var project_id      = $('#project_id').val();
    var action = 'reset_tracks';
    var postdata = {'project_id': project_id,'action': action};
    ajax_send(postdata);    
} );

// ajax send //
function ajax_send(post_data)
{

    $.ajax({
        url: CI_ROOT + 'private/validator/update_files',
        type: 'post',
        data: post_data ,
        complete: function(r){
            var response_obj = jQuery.parseJSON(r.responseText);
            if (response_obj.status == 'FAIL')
            {
                set_message('response_message_validator',response_obj.data.message, 'error');
            }
            else
            {
                window.location.reload();
            }
        },

    });


}


function validate_dir_name(validator_dir)
{
	var valid = false;

	if (validator_dir.length < 1) return false;

	var characterReg = /^[a-z0-9A-Z_\-]+$/;
	if(!characterReg.test(validator_dir)) return false;

	return true;
}

function save_validator_dir(project_id, validator_dir)
{
	var created = false;
    $.ajax({
        url: CI_ROOT + 'private/validator/save_validator_dir',
        type: 'post',
        async: false,
        data: {"project_id": project_id, "validator_dir": validator_dir },
        complete: function(r){
            var response_obj = jQuery.parseJSON(r.responseText);
            if (response_obj.status == 'FAIL')
            {
            	set_message('response_message_validator',response_obj.data.message, 'error');
            	created = false;
            }
            else
            {
            	created = true;
            }
        },

    });
    return created;
}

function copy_project_files(project_id)
{
	var created = false;
    $.ajax({
        url: CI_ROOT + 'private/validator/copy_project_files',
        type: 'post',
        async: false,
        //beforeSend: show_loader(),
        data: {"project_id": project_id },
        complete: function(r){
            //hide_loader();
            var response_obj = jQuery.parseJSON(r.responseText);
            if (response_obj.status == 'FAIL')
            {                
            	set_message('response_message_validator',response_obj.data.message, 'alert-error');
            	created = false;
            }
            else
            {
            	created = true;
            }
        },

    });
    return created;
}

function get_file_tags(project_id, file_name)
{
    var section = false;
    $.ajax({
        url: CI_ROOT + 'private/validator/get_file_tags',
        type: 'post',
        async: false,
        data: {"project_id": project_id, 'file_name': file_name },
        complete: function(r){
            var response_obj = jQuery.parseJSON(r.responseText);
            if (response_obj.status == 'FAIL')
            {
                set_message('response_message_validator',response_obj.data.message, 'error');
                section = false;
            }
            else
            {
                section = response_obj.data;
            }
        },

    });
    return section;
}


/* ********* Deleting files ********************* */

$('.delete_file').on('click', function(){
    var file_name = $(this).attr('data-file_name');
    var project_id  = $('#project_id').val();

    //confirm
    jConfirm('Can you confirm this?', 'Confirmation Dialog', function(r) {
        //jAlert('Confirmed: ' + r, 'Confirmation Results');

        if (r)
        {
            var result = delete_file(project_id, file_name);
            if(result) window.location.reload();
        }    
    });


});

function delete_file(project_id, file_name)
{
    var result = false;
    $.ajax({
        url: CI_ROOT + 'private/validator/delete_file',
        type: 'post',
        async: false,
        data: {"project_id": project_id, 'file_name': file_name },
        complete: function(r){
            var response_obj = jQuery.parseJSON(r.responseText);
            if (response_obj.status == 'FAIL')
            {
                set_message('response_message_validator',response_obj.data.message, 'error');
                result = false;
            }
            else
            {
                result = true;
            }
        },

    });
    return result;    
}

/* ******* Upload to Archive.org modal *************  */

$('#upload_iarchive').on('click', function(){
    $('#uploader_modal').modal();    
});

$('#iarchive_uploader_submit').on('click', function(){
    $.ajax({
        url: CI_ROOT + 'private/iarchive_upload/upload',
        type: 'post',
        async: false,
        data: $('#uploader_form').serialize(),
        complete: function(r){
            var response_obj = jQuery.parseJSON(r.responseText);
            if (response_obj.status == 'FAIL')
            {
                set_message('response_message_uploader',response_obj.data.message, 'alert-error');
            }
            else
            {
                var message = '<button type="button" class="close" data-dismiss="alert">&times;</button>Success! </br> Your files have been uploaded to ' + response_obj.data.link + '. <br /> It may be a few minutes before the IArchive processess has completely finished.'
                set_message('response_message_validator', message, 'alert-success');
                $('#uploader_modal').modal('hide');
            }
        },

    });
});


// *********  Run tests **********//

$('#run_tests').on('click', function(){
    var project_id  = $('#project_id').val();
    run_tests(project_id);
    
});

function run_tests(project_id)
{
    $.ajax({
        url: CI_ROOT + 'private/validator/run_tests',
        type: 'post',
        async: false,
        data: {"project_id": project_id},
        complete: function(r){
            var response_obj = jQuery.parseJSON(r.responseText);
            if (response_obj.status == 'FAIL')
            {
                set_message('response_message_validator',response_obj.data.message, 'error');
                result = false;
            }
            else
            {
                var message = '<div class="alert alert-info"><button type="button" class="close" data-dismiss="alert">&times;</button><h4>Test Results:</h4>';
                message = message + build_results(response_obj.data.formatted_results)+'</div>';

                $('#run_tests_div').html(message).show();
                result = true;
            }
        },

    });

}

function build_results(results)
{
    var html = check = '';
    jQuery.each(results, function(index, result_list){

        check = (result_list.length == 0) ? '<span style="color:green;">OK</span>': '<span style="color:red;">The following files failed this test</span>';

        var label = index.replace('_', ' ');
        label = label.charAt(0).toUpperCase() + label.slice(1) + ': '; //can't chain?

        
        html = html + '<h5 class="test_result_section" data-index="'+index+'" style="cursor:pointer;">'+ label + ' ' + check + '</h5>';
        html = html + '<div id="test_result_section_'+index+'"  style="display:none;">';

        if (result_list.length > 0)
        {
            jQuery.each(result_list, function(inner_index, failed_test){
                html = html + '<p>' + failed_test + '</p>';
            });
        }    

        html = html + '</div>';

    });
    return html;

}

$('.test_result_section').live('click', function(){
    var index = $(this).attr('data-index');
    $('#test_result_section_'+index).toggle();
});