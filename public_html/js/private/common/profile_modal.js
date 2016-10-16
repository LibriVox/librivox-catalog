$(document).ready(function(){
	$('.profile_modal_link').on('click', function(){
      var link = $(this).attr('id');
      switch (link)
      {
        case 'profile_modal_link':
          get_profile(0);
          break;
        case 'addnew_modal_link':
          new_profile(link);
          break;
        case 'add_reader_toggle':
        default:
          add_reader('add reader');
      }
      
	});

  $('#edit_profile_submit').on('click', function(){
      $('#response_message').html('');
      edit_profile();
  });
});


var reader = 3;  //value in db; no reason this will ever change

//we migth move & combine with general edit profile js
function get_profile(user_id){

  $.ajax({
      url: CI_ROOT + "private/user/get_profile",
      type: 'post',
      data: {'user_id': user_id},
      complete: function(r){
        var response_obj = jQuery.parseJSON(r.responseText);
        
        if (response_obj.status == 'SUCCESS')
        {
            populate_form(response_obj.data.user);
            $('#profile_modal').modal('show');
        }
        else
        {       
            $('#response_message').html(response_obj.data.message);
        }
      },
  });     
};

function new_profile(link)
{
    $('#profile_form')[0].reset();

    $('#profile_label').html('Add new user');
    $('#profile_form #action').val('add');

    //a few differences...
    $('#profile_form #username').removeAttr('disabled');  
     _change_control_and_label($('#profile_form #max_projects'), 'hide');

    _change_control_and_label($('#profile_form #email'), 'hide');

    $('#profile_form #password_label').hide();
    _change_control_and_label($('#profile_form #password'), 'hide');
    _change_control_and_label($('#profile_form #confirm_password'), 'hide');
   
    $('#edit_profile_submit').html('Add user');

     _uncheck_all_groups();
    $('#profile_form #groups_block').show();

    $('#profile_modal').modal('show');
}

function populate_form(user)
{

    $('#profile_label').html(user.display_name);

    $('#profile_form #action').val('update');

    $('#profile_form #username').val(user.username);
    $('#profile_form #user_id').val(user.id);
    

    $('#profile_form #display_name').val(user.display_name);
    $('#profile_form #email').val(user.email);
    $('#profile_form #website').val(user.website);
    $('#profile_form #max_projects').val(user.max_projects).show();

     _change_control_and_label($('#profile_form #max_projects'), 'show');

     // hide, and only show if own record
    $('#profile_form #password_label').hide();
    _change_control_and_label($('#profile_form #password'), 'hide');
    _change_control_and_label($('#profile_form #confirm_password'), 'hide');
    $('#profile_form #password').val('');
    $('#profile_form #confirm_password').val('');

    if (user.show_password == true)
    {
      $('#profile_form #password_label').show();
      _change_control_and_label($('#profile_form #password'), 'show');
      _change_control_and_label($('#profile_form #confirm_password'), 'show');


    }

    //always hide, then show if needed - security is on controller, this is just simpler
    $('#profile_form #groups_block').hide();  
    if (user.show_groups == true)
    {
        _uncheck_all_groups();
        _fill_user_permissions(user.user_groups);
        $('#profile_form #groups_block').show();
    } 
    

    $('#edit_profile_submit').html('Save changes');

}

function _uncheck_all_groups()
{
    $('.group_box').attr('checked', false);
}

function _fill_user_permissions(user_groups){
    jQuery.each(user_groups, function(i, value){
      $('#groups_' + value.id).attr('checked', true);
    });
}

function _change_control_and_label(element, state)
{
    if (state == 'hide')
    {
      element.hide();      
      element.closest('label').hide();      
    }
    else
    {
      element.show();
      element.closest('label').show();       
    }  

}


function edit_profile(){

  //less spaghetti is we make separate functions...
  var action = $('#profile_form #action').val();

  $.ajax({
      url: CI_ROOT + "private/user/" + action + "_profile",
      type: 'post',
      data: $('#profile_form').serialize(),
      complete: function(r){
        var response_obj = jQuery.parseJSON(r.responseText);
        
        if (response_obj.status == 'SUCCESS' || response_obj.data.status == 'SUCCESS')
        {
            $('#profile_modal').modal('hide');
            $('#response_message').html('');
        }
        else
        {       
            $('#response_message').html(response_obj.data.message).show();
        }
      },
  });     
};


function add_reader(link)
{
    $('#profile_form')[0].reset();

    $('#profile_label').html('Add new reader');
    $('#profile_form #action').val('add');

     _change_control_and_label($('#profile_form #email'), 'hide');

    //a few differences...
    $('#profile_form #username').removeAttr('disabled');  
     _change_control_and_label($('#profile_form #max_projects'), 'hide');

    $('#profile_form #password_label').hide();
    _change_control_and_label($('#profile_form #password'), 'hide');
    _change_control_and_label($('#profile_form #confirm_password'), 'hide');
   
    $('#edit_profile_submit').html('Add reader');

    //always hide, then show if needed - security is on controller, this is just simpler
    $('#profile_form #groups_block').hide();  
    _uncheck_all_groups();
    //check reader
    $('#groups_' + reader).attr('checked', true);

    $('#profile_modal').modal('show');
}