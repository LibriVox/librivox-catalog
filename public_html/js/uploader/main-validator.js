//this is the application.js file from the example code//
$(function () {
    'use strict';

    // Initialize the jQuery File Upload widget:
    $('#fileupload').fileupload({
        submit: function (e, data) {
            //validate - check an MC was chosen
            var mc = $('#mc').val();
            if(mc == '')
            {
                alert('Please select an MC before uploading');
                return false;
            } 
        }, // .bind('fileuploadsubmit', func);
        done: function (e, data){
            // there is a lot of binding & re-tagging of files, so a full refresh is much safer for the moment

            window.location.reload();
        }   
    });
    
    // Enable iframe cross-domain access via redirect option:
    $('#fileupload').fileupload(
        'option',
        'redirect',
        window.location.href.replace(
            /\/[^\/]*$/,
            './cors/result.html?%s'
            )
    );

//Set your url localhost or your ndd (perrot-julien.fr)
    if (window.location.hostname === 'localhost') {
        //Load files
        // Upload server status check for browsers with CORS support:
        if ($.ajaxSettings.xhr().withCredentials !== undefined) {
            $.ajax({
                url: 'upload/get_files',
                dataType: 'json', 
                
                success : function(data) {  

                    var fu = $('#fileupload').data('fileupload'), 
                    template;
                    fu._adjustMaxNumberOfFiles(-data.length);
                    template = fu._renderDownload(data)
                    .appendTo($('#fileupload .files'));
                    
                    // Force reflow:
                    fu._reflow = fu._transition && template.length &&
                    template[0].offsetWidth;
                    template.addClass('in');
                    $('#loading').remove();
                }  
         
                
            }).fail(function () {
                $('<span class="alert alert-error"/>')
                .text('Upload server currently unavailable - ' +
                    new Date())
                .appendTo('#fileupload');
            });
        }
    } 
    /*
    else {
        // Load existing files:
        $('#fileupload').each(function () {
            var that = this;
            $.getJSON(this.action, function (result) {
                if (result && result.length) {
                    $(that).fileupload('option', 'done')
                    .call(that, null, {
                        result: result
                    });
                }
            });
        });
    }
    */


    // Open download dialogs via iframes,
    // to prevent aborting current uploads:
    $('#fileupload .files a:not([target^=_blank])').live('click', function (e) {
        e.preventDefault();
        $('<iframe style="display:none;"></iframe>')
        .prop('src', this.href)
        .appendTo('body');
    });

});