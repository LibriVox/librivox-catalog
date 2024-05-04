set_autocomplete();

function set_autocomplete() {
	var search_area;
    var array_index;

	$( ".autocomplete" ).autocomplete({
        source: function(request, response) {

			var element = this.element;
			var search_func = element.attr('data-search_func');
			var search_field = element.attr('data-search_field');
            array_index = element.attr('data-array_index');
			search_area = element.attr('data-search_area');

            var filter_element_id = element.attr('data-filter_element');
            if (filter_element_id) {
                var filter_element_obj = $('input[id^="' + filter_element_id + '"]')[0];
                var filter_field = filter_element_obj.attributes['data-search_field'].value;
                var filter_term = filter_element_obj.value;
            }

            $.ajax({ url: CI_ROOT + 'public/public_ajax/' + search_func,
                data: { 'term': element.val(), 'search_field': search_field,
                    'filter_field': filter_field, 'filter_term':  filter_term},
                dataType: "json",
                type: "POST",
				global: false, // prevent modal loader dialog
                success: function (data) {
                    if (data != null) {
                        response($.map(data, function (item) {
							return autocomplete_assign_vars(item);
                        }))	
                    } else $(".ui-autocomplete").css({
                        "display": "none"
                    });
                },
                error: function (data) {
                    alert('Error: ' + data.errorThrown);
                }
            });
        },
        minLength: 1,
        select: function (event, ui) {
			autocomplete_assign_elements(search_area, ui, array_index);
            return false;
        }
	});
}	

	/*
	.data('autocomplete')._renderItem() = function (ul, item) { 
				return $("<li></li>").data("item.autocomplete", item).prepend("<a>" + item.label + "</a>").appendTo(ul);
			}
	*/

