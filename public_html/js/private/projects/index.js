
$(document).ready(function(){




	var oTable = $('#projects_table').dataTable({
		"oLanguage": {"sSearch": "Refine your search: "},
        "bFilter": true,
        "bPaginate" : true,
        "bInfo" : true,
        "sPaginationType": "full_numbers",
        "iDisplayLength" : 10,
		"aoColumnDefs": [
            { "bVisible": false, "bSearchable": true, "aTargets": [ 0 ] }
        ] ,


  	});


	var search_filter = $('.status_group').val();
  	//oTable.fnFilter( search_filter , 0 );


	$('.status_group').live('click', function(){
		if ($(this).val() == '1') 
		{
			oTable.fnFilter( $(this).val(), 0 );
		}
		else
		{
			oTable.fnFilter('', 0);
			//oTable.fnFilter( '' );
		}	
	}) ; 


} );