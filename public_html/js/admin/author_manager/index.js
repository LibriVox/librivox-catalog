
/*
$.fn.dataTableExt.oApi.fnStandingRedraw = function(oSettings) {
    if(oSettings.oFeatures.bServerSide === false){
        var before = oSettings._iDisplayStart;
 alert(before);
        oSettings.oApi._fnReDraw(oSettings);
 
        // iDisplayStart has been reset to zero - so lets change it back
        oSettings._iDisplayStart = before;
        oSettings.oApi._fnCalculateEnd(oSettings);
    }
      
    // draw the 'current' page
    oSettings.oApi._fnDraw(oSettings);
};

oTable.fnStandingRedraw();

*/


$(document).ready(function() {

    bind_edit();

    $.extend( $.fn.dataTable.defaults, {
        "bFilter": true,
        "bPaginate" : true,
        "bInfo" : true,
        "sPaginationType": "full_numbers",
        "aoColumnDefs": [
            { "bVisible": false, "bSearchable": true, "aTargets": [ 0 ] }
        ] ,
    } );



    var oTable = $('#authors_table').dataTable();

    oTable.fnSort([[1, 'asc']]);
    

    //default  -only unconfirmed
    // need to make this check which radio button is checked
    var checked_radio = $('input[class=status_group]:checked').val();    
    apply_filter(checked_radio);  

    $('.status_group').live('click', function(){   
        apply_filter( $(this).val());
    }) ; 

    function apply_filter(filter_value)
    {
        if (filter_value == '1') 
        {
            oTable.fnFilter( 0, 0 );
        }
        else
        {
            oTable.fnFilter('', 0);
            //oTable.fnFilter( '' );
        }   
    }

    function bind_edit()
    {
        $('.edit').editable(CI_ROOT + "admin/author_manager/update_author_value",
            {
                indicator : 'Saving...',
                tooltip   : 'Double-click to edit...',
                placeholder: '',
                event: 'dblclick',
                select : true,
                callback : function(value, settings) {
                    // console.log(this.id);
                    // console.log(value);
                    // console.log(settings);
                    var split_id = this.id.split('-');
                    if (split_id[0] == 'linked_to')
                    {
                        this_td = $('#'+this.id);
                        var deleted_row = this_td.closest('tr').get(0);

                        oTable.fnDeleteRow(
                            oTable.fnGetPosition(
                                deleted_row
                            )
                        );

                    }

                }
            }
        );        
    }


    function update_author_status(txt, row_id)
    {
        oTable.fnUpdate(txt, row_id, 0);
    }


    $('.confirm_author').live('click', function(){

        var this_btn = $(this);
        var id = this_btn.attr('id');
        var value = this_btn.attr('data-status');
        value = Math.abs(value -1);
        
        $.ajax({
              url: CI_ROOT + 'admin/author_manager/update_author_value',
              type: 'post',
              data: {'value' : value, 'id' : id },
              complete: function(r){
                var response_obj = jQuery.parseJSON(r.responseText);

                this_btn.removeClass('btn-success').attr('data-status', value).html('Confirm');
                if (response_obj)
                {
                    this_btn.addClass('btn-success').html('Reopen');
                }

                var row = this_btn.closest('tr').get(0);
                var row_id = oTable.fnGetPosition(row);

                update_author_status(value, row_id);

                //the one from above was static
                var sub_checked_radio = $('input[class=status_group]:checked').val();
                apply_filter(sub_checked_radio);
     
              },
        });   

    });

    /* *****  New author  ****** */
    $('#new_author_modal_btn').on('click', function(){
        $('#add_new_author_form')[0].reset();
        $('#author_new_modal').modal();
    });

    $('#author_new_submit').on('click', function(){
        $.ajax({
              url: CI_ROOT + 'admin/author_manager/add_new_author',
              type: 'post',
              data: $('#add_new_author_form').serialize(),
              complete: function(r){
                //var response_obj = jQuery.parseJSON(r.responseText);

                alert('You will need to refresh the page in order to see your newly added author. This can be slow, so we let you do it manually when you are ready.')
                $('#author_new_modal').modal('hide');
     
              },
        }); 
    });

    
    /* *****  Blurb edit  ****** */

    $('.blurb_edit').live('dblclick', function(){
        var author_id = $(this).attr('data-author_id');
        var author_name = $(this).attr('data-author_name');
        var author_blurb = $(this).html();

        $('#modal_author_name').html(author_name);
        $('#author_id').val(author_id);
        $('#blurb').val(author_blurb);

        $('#author_blurb_modal').modal();

    });    


    $('#author_blurb_submit').live('click', function(){
        var author_id   = $('#author_id').val();
        var author_name = $(this).attr('data-author_name');
        var id          = 'blurb-'+ author_id;
        var value       = $('#blurb').val();
        var row = document.getElementById('author_row_'+ author_id); 

        var cell_value = '<div id="blurb_'+author_id+'" class="blurb_edit tdfield" data-author_id="'+author_id+'" data-author_name="'+author_name+'">'+value+'</div>';
        
        $.ajax({
              url: CI_ROOT + 'admin/author_manager/update_author_value',
              type: 'post',
              data: {'value' : value, 'id' : id },
              complete: function(r){
                var response_obj = jQuery.parseJSON(r.responseText);

                var rownum = oTable.fnGetPosition( row );
                oTable.fnUpdate(cell_value, rownum, 6);

                $('#modal_author_name').html('');
                $('#author_blurb_modal').modal('hide');
     
              },
        });   

    });


    /* *****  Pseudonyms edit  ****** */

    $('.pseudonyms_edit').live('dblclick', function(){
        var author_id = $(this).attr('data-author_id');
        var author_name = $(this).attr('data-author_name');

        $('#pseudonyms_author_name').html(author_name);
        $('.submit_pseudonym').attr('data-author_id', author_id);
        $('#pseudonyms_list').html('');

        //clear old values
        $('#pseudo_first_name_0').val('');
        $('#pseudo_last_name_0').val('');

        $.ajax({
              url: CI_ROOT + 'admin/author_manager/get_author_pseudonyms',
              type: 'post',
              data: {'author_id' : author_id },
              complete: function(r){
                var response_obj = jQuery.parseJSON(r.responseText);

                
                var author_pseudonyms = response_obj.data.author_pseudonyms;
                var html = _pseudonym_build_table(author_pseudonyms);

                $('#pseudonyms_list').html(html);
                $('#author_pseudonyms_modal').modal();
     
              },
        }); 

    });


    function _pseudonym_build_table(author_pseudonyms)
    {
        var html = '<table>'; 
        jQuery.each(author_pseudonyms, function(index, value){
            html = html + _pseudonym_row_template(value);
        });  

        html = html + '<table>'; 
        return html;      
    }


    function _pseudonym_row_template(value)
    {
        var html = '<tr>';
        html = html + '<td><input id="pseudo_first_name_'+value.id+'" value="' + value.first_name + '" /></td>'
        + '<td><input id="pseudo_last_name_'+value.id+'" value="' + value.last_name + '" /></td>'
        + '<td><input type="button" class="submit_pseudonym btn" data-pseudonym_id="'+value.id+'" data-author_id="'+value.author_id+'" value="Save">'
        + '<input type="button" class="remove_pseudonym btn" data-pseudonym_id="'+value.id+'"  value="Remove"></td>';
        html = html + '<tr>';

        return html;
    }




    $('.submit_pseudonym').live('click', function(){

        var id = $(this).attr('data-pseudonym_id');
        var author_id = $(this).attr('data-author_id');
        var first_name = $('#pseudo_first_name_'+ id).val();
        var last_name = $('#pseudo_last_name_'+ id).val();
        
        $.ajax({
              url: CI_ROOT + 'admin/author_manager/update_add_pseudonym',
              type: 'post',
              data: {'id' : id, 'author_id' : author_id, 'first_name': first_name, 'last_name': last_name },
              complete: function(r){
                var response_obj = jQuery.parseJSON(r.responseText);
                
                $('#author_pseudonyms_modal').modal('hide');
                alert(response_obj.data.message);
              },
        });   

    });


    $('.remove_pseudonym').live('click', function(){

        var id = $(this).attr('data-pseudonym_id');
        
        $.ajax({
              url: CI_ROOT + 'admin/author_manager/delete_pseudonym',
              type: 'post',
              data: {'id' : id },
              complete: function(r){
                var response_obj = jQuery.parseJSON(r.responseText);
                
                $('#author_pseudonyms_modal').modal('hide');
                alert(response_obj.data.message);
              },
        });   

    });




    /* *****  Projects view  ****** */

    $('.project_link').live('dblclick', function(){
        var author_id = $(this).attr('id');
        var author_name = $(this).attr('data-author_name');

        $('#projects_author_name').html(author_name);
        $('#project_list').html('');

        $.ajax({
              url: CI_ROOT + 'admin/author_manager/get_author_projects',
              type: 'post',
              data: {'author_id' : author_id },
              complete: function(r){
                var response_obj = jQuery.parseJSON(r.responseText);

                var html = '';
                var projects = response_obj.data.projects;
                jQuery.each(projects, function(index, value){
                    html = html + value.id + ' - ' + value.title + '<br />';
                });

                $('#project_list').html(html);
                $('#author_projects_modal').modal();
     
              },
        }); 

    });

});