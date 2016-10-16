$(document).ready(function() {
	//bind_edit();

    $.extend( $.fn.dataTable.defaults, {
        "bFilter": true,
        "bPaginate" : true,
        "bInfo" : true,
        "sPaginationType": "full_numbers"
    } );


	var oTable = $('#authors_table').dataTable({
    	fnDrawCallback: function( oSettings ) {
      		$('#authors_table td').addClass('edit');
    	}
  	});


	// Return a helper with preserved width of cells
	var fixHelper = function(e, ui) {
		ui.children().each(function() {
			$(this).width($(this).width());
		});
		return ui;
	};

	$("#authors_table tbody").sortable({
		helper: fixHelper,
		update: function(){

		}
	}).disableSelection();

});