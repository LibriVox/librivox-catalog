$(document).ready(function() {

	bind_edit();

	var oTable = $('#authors_table').dataTable();

    function bind_edit()
    {
        $('.edit').editable(CI_ROOT + "admin/author_manager/unknownfunction",
            {
                indicator : 'Saving...',
                tooltip   : 'Double-click to edit...',
                placeholder: '',
                event: 'dblclick',
                select : true,

            }
        );        
    }


	$('#authors_table tbody').on('keydown', 'input,select,textarea', function(e){

            // If tab key
            if(e.keyCode == 9) {

                e.preventDefault();
                e.stopPropagation()
                 
                oInput = $(this);

                // Call fnNextInput with all the cells in the row to the right of the current cell
                fnNextInput(oInput.closest('td').nextAll());
            }
        });


        // Function to find next editable cell in row
        function fnNextInput(oTds) {
             
            // Loop through cells
            oTds.each(function(){
                 
                // Get column position of cell
                iColPos = oTable.fnGetPosition(this)[1];
                 
                // If cell is editable then click and focus on input
                if (options.columnEditSettings[iColPos] != null) 
                {        // NOTE: options.colu... is my own object that I am passing to makeEditable()
                    $(this).click().find('input,select,textarea').focus();
                    return false;
                }
                 
                // If end of row, go to next row
                if(iColPos == options.columnEditSettings.length -1) {
                    fnNextInput($(this).closest('tr').next().children());
                    return false;
                }
            });
        }






});