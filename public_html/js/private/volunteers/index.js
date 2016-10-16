$(document).ready(function() {


	//bind_edit();

    $.extend( $.fn.dataTable.defaults, {
        "bFilter": true,
        "bPaginate" : true,
        "bInfo" : true,
        "sPaginationType": "full_numbers"
    } );


	var oTable = $('#volunteers_table').dataTable({
		"oLanguage": {"sSearch": "Refine your search: "},
    	fnDrawCallback: function( oSettings ) {
      		$('#volunteers_table td').addClass('edit');
    	}
  	});


	// Return a helper with preserved width of cells
	var fixHelper = function(e, ui) {
		ui.children().each(function() {
			$(this).width($(this).width());
		});
		return ui;
	};

	$("#volunteers_table tbody").sortable({
		helper: fixHelper,
		update: function(){

		}
	}).disableSelection();

	$('.user_meta_data').live('click', function(){
		var user_id = $(this).attr('data-volunteer_id');
		get_profile(user_id);  //function on application.js
	});


});	