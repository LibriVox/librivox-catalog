$(document).ready(function() {

	//update_group
	$('.update_group').on('click', function(){
		var form = $(this).closest('form');

        $.ajax({
              url: CI_ROOT + 'private/groups/update_group',
              type: 'post',
              data: form.serialize(),
              complete: function(r){
                var response_obj = jQuery.parseJSON(r.responseText);
                window.location.reload();
     
              },
        });

	});
   
	//delete_group
	$('.delete_group').on('click', function(){
		var group_id = $(this).attr('data-id');

        $.ajax({
              url: CI_ROOT + 'private/groups/delete_group',
              type: 'post',
              data: {'group_id': group_id},
              complete: function(r){
                var response_obj = jQuery.parseJSON(r.responseText);
                //window.location.reload();
     
              },
        });

	});
	//
	$('.delete_project').on('click', function(){
		var group_id = $(this).attr('data-group_id');
		var project_id = $(this).attr('data-project_id');


        $.ajax({
              url: CI_ROOT + 'private/groups/remove_project',
              type: 'post',
              data: {'group_id': group_id, 'project_id':project_id},
              complete: function(r){
                var response_obj = jQuery.parseJSON(r.responseText);
                window.location.reload();
     
              },
        });

	});
});