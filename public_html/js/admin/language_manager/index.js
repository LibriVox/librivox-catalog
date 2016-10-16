
$(document).ready(function() {

    bind_edit();

    $.extend( $.fn.dataTable.defaults, {
        "bFilter": true,
        "bPaginate" : true,
        "bInfo" : true,
        "sPaginationType": "full_numbers",
        "bSort": false,

    } );

    var oTable = $('#languages_table').dataTable();

    function bind_edit()
    {
        $('.edit').editable(CI_ROOT + "admin/language_manager/update_language_value",
            {
                indicator : 'Saving...',
                tooltip   : 'Double-click to edit...',
                placeholder: '',
                event: 'dblclick',
                select : true,

            }
        );        
    }


    $('.toggle_language').live('click', function(){

        var this_btn = $(this);
        var id = this_btn.attr('id');
        var value = this_btn.attr('data-status');
        value = Math.abs(value -1);
        
        $.ajax({
              url: CI_ROOT + 'admin/language_manager/update_language_value',
              type: 'post',
              data: {'value' : value, 'id' : id },
              complete: function(r){
                var response_obj = jQuery.parseJSON(r.responseText);

                this_btn.removeClass('btn-success').attr('data-status', value).html('No');
                if (response_obj)
                {
                    this_btn.addClass('btn-success').html('Yes');
                }
     
              },
        });   

    });

    $('.save_language').live('click', function(e){

        e.preventDefault();

        var form = $(this).closest('form');
        
        $.ajax({
              url: CI_ROOT + 'admin/language_manager/add_language',
              type: 'post',
              data: form.serialize(),
              complete: function(r){
                var response_obj = jQuery.parseJSON(r.responseText);

                form.each (function(){
                   this.reset();
                }); 

                window.location.reload();
             
              },
        });   

    });


});