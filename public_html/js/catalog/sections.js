

$('.data_tab').on('click', function(){
	$('.data_tab').removeClass('active');
	$(this).addClass('active');

	$('.tab-pane').removeClass('active').hide();

	var active_pane = $(this).attr('data-tab');
	$('#'+ active_pane ).show();

	return false;
});