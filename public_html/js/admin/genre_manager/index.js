

$('.save_genre').live('click', function(){

    var genre_id 	= $(this).attr('data-genre_id');
    var name 		= $('#genre_'+ genre_id).val();
    var parent_id 	= $('#parent_id_'+ genre_id).val();
    
    $.ajax({
          url: CI_ROOT + 'admin/genre_manager/update_genre',
          type: 'post',
          data: {'id' : genre_id, 'name' : name, 'parent_id': parent_id },
          complete: function(r){
            var response_obj = jQuery.parseJSON(r.responseText);

            window.location.reload();
		 
          },
    });   

});