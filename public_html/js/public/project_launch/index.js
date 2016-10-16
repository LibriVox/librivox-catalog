/**
 * @author JMadsen
 */
$(document).ready(function(){

    $('form').bind("keypress", function(e) {
        var code = e.keyCode || e.which; 
        if (code  == 13) {               
            e.preventDefault();
            return false;
        }
    });



    //we'd like to completely clear the author data (including id) if the first or last name is deleted
    $('form').bind("keydown", function(e) {  //NOT keypress
        var code = e.keyCode || e.which; 
        var $focused = $(':focus');

       // console.log(code);
       // console.log($focused.val());

        if (code  == 46 || (code == 8 && $focused.val().length == 1)) {     // note - it is still 1 at this point, not yet fully deleted          

            var array_index = $focused.attr('data-array_index');

            if (array_index != 'undefined')
            {

                var type = $focused.attr('data-search_area');
                type = (type == 'author') ? 'auth': 'trans';
   
                $('input[id^="'+type+'_first_name"][data-array_index="'+array_index+'"]').val('');
                $('input[id^="'+type+'_last_name"][data-array_index="'+array_index+'"]').val('');
                $('input[id^="'+type+'_yob"][data-array_index="'+array_index+'"]').val('');
                $('input[id^="'+type+'_yod"][data-array_index="'+array_index+'"]').val('');
                $('input[id^="link_to_'+type+'"][data-array_index="'+array_index+'"]').val('');
                $('input[id^="'+type+'_id"][data-array_index="'+array_index+'"]').val('');
                $('a[id^="link_to_'+type+'_link"][data-array_index="'+array_index+'"]').attr('href', '#');
                
            }    

 
        }
    });    



    $('#list_keywords').tagsInput({
       'height':'100px',
       'width':'770px',
    });


    $('#lang_select').live('change', function(){
	  	var language = $(this).val();
		
        $.ajax({
            url: 'public/public_ajax/language_switcher',
            data:{
                'lang_code': language
            },
            type: 'POST',
            complete: function(){location.reload();}
        
        });        
    });

    $('#project_type').live('change', function(){
        var arr = [ 'solo', 'poetry_weekly', 'poetry_fortnightly'];

        if (jQuery.inArray($(this).val(), arr) != -1)
        {
            $('#completion_date_block').show();
        }
        else
        {
            $('#completion_date_block').hide();
        }    
        
    });

    $('#recorded_language').live('change', function(){
        if ($('#recorded_language option:selected').text() == 'Other')
        {
            $('#recorded_language_other').show();
            $('#recorded_language_other_label').show();

        }
        else
        {
            $('#recorded_language_other').hide();
            $('#recorded_language_other_label').hide();
        }            
    }); 


    $("#add_project").validate({

        onkeyup: false,

        invalidHandler: function(form, validator){
            if (validator.numberOfInvalids() > 0)
            {
                $("#showErrors").html("<h4>Your form contains "
                           + validator.numberOfInvalids() 
                           + " errors, see details below.</h4>").css('color', 'red');
                validator.defaultShowErrors();
            }            
        },

        errorPlacement: function(error, element) {
            element.val('');
            element.attr('placeholder', error.text()).addClass("form_error");           
        },

        highlight: function(element, errorClass) {
            $(element).addClass("form_error");
        },

        rules: {
            title: "required",
            link_to_text: "required",
            "auth_last_name[1]": "required",
        },

        messages: {
            auth_yod: "Please enter a 4 digit year",
            auth_yob: "Please enter a 4 digit year",
            trans_yob: "Please enter a 4 digit year",
            trans_yod: "Please enter a 4 digit year",
        },

    });


    $("#add_project").on('click', function(){
        $('.valid').removeClass("form_error");
    });


    $("#add_author").on('click', function(){
        var counter = $(this).attr('data-counter');   

        $.ajax({
            url: CI_ROOT + 'public/public_ajax/add_author',
            type: 'post',
            async:   false, 
            data: {"counter": counter },
            complete: function(r){
                var response_obj = jQuery.parseJSON(r.responseText);

                $('#author_blocks').append(response_obj.html);
                set_autocomplete();
                $("#add_author").attr('data-counter', parseInt(counter) +1);
            },

        });

    });

    $("#add_translator").on('click', function(){
        var counter = $(this).attr('data-counter');   

        $.ajax({
            url: CI_ROOT + 'public/public_ajax/add_translator',
            type: 'post',
            async:   false, 
            data: {"counter": counter },
            complete: function(r){
                var response_obj = jQuery.parseJSON(r.responseText);

                $('#translator_blocks').append(response_obj.html);
                set_autocomplete();
                $("#add_translator").attr('data-counter', parseInt(counter) +1);
            },

        });

    });


    //move to generic file



});


function assign_vars(item)
{
    if (!item.dob) {item.dob = '';}
    if (!item.dod) {item.dod = '';}

    var lifespan = '';
    if (item.dob || item.dod){lifespan = '  (' + item.dob + ' - ' + item.dod + ')';}

    return {
        label: item.first_name + ' ' + item.last_name + lifespan,
        value: item.first_name,
        first_name: item.first_name,
        last_name: item.last_name,
        dob: item.dob,
        dod: item.dod, 
        author_url: item.author_url, 
        author_id: item.id,                                   
    }
}

function assign_elements(search_area, ui, array_index)
{
    switch (search_area)
    {
        case 'author':

            $('input[id^="auth_first_name"][data-array_index="'+array_index+'"]').val(ui.item.first_name);
            $('input[id^="auth_last_name"][data-array_index="'+array_index+'"]').val(ui.item.last_name);
            $('input[id^="auth_yob"][data-array_index="'+array_index+'"]').val(ui.item.dob);
            $('input[id^="auth_yod"][data-array_index="'+array_index+'"]').val(ui.item.dod);
            $('input[id^="link_to_auth"][data-array_index="'+array_index+'"]').val(ui.item.author_url);
            $('input[id^="auth_id"][data-array_index="'+array_index+'"]').val(ui.item.author_id);
            $('a[id^="link_to_auth_link"][data-array_index="'+array_index+'"]').attr('href', ui.item.author_url);    
            break;
        case 'translator':
            $('input[id^="trans_first_name"][data-array_index="'+array_index+'"]').val(ui.item.first_name);
            $('input[id^="trans_last_name"][data-array_index="'+array_index+'"]').val(ui.item.last_name);
            $('input[id^="trans_yob"][data-array_index="'+array_index+'"]').val(ui.item.dob);
            $('input[id^="trans_yod"][data-array_index="'+array_index+'"]').val(ui.item.dod);
            $('input[id^="link_to_trans"][data-array_index="'+array_index+'"]').val(ui.item.author_url);
            $('input[id^="trans_id"][data-array_index="'+array_index+'"]').val(ui.item.author_id);
            $('a[id^="link_to_trans_link"][data-array_index="'+array_index+'"]').attr('href', ui.item.author_url);    

            break;
    }
}