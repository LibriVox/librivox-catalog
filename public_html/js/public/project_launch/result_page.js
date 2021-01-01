function resize_textarea() {
	var $textarea = $("#textarea");
	var h = $(window).height();
	var t = $textarea.position().top;
	$textarea.height(Math.max(h - t - 40, 100));
}

$(document).ready(function() {
	$(window).resize(function() {
		resize_textarea();
	});

	$("#copy").click(function() {
		var $textarea = $("#textarea");

		try {
			navigator.clipboard.writeText($textarea.val());
		} catch(e) {
			$textarea.focus().select();
			document.execCommand("copy");
		}
	});

	resize_textarea();
	$("#textarea").css("opacity", "");
});
