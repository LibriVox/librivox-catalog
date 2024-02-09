<?php

/**
 * For a while, newlines weren't being rendered properly in the catalog HTML,
 * so people put manual <br /> tags into project descriptions. Now, we want to
 * render the newlines properly, so we need to try and dedupe a bit so that we
 * don't have crazy amounts of whitespace.
 *
 * Inspired by this post: https://www.darklaunch.com/php-normalize-newlines-line-endings-crlf-cr-lf-unix-windows-mac.html
 *
 * $description string The description to render as HTML
 */
function _normalize_and_deduplicate_newlines_in_html($description) {
	// Normalise everything to '\n' characters
	$description = str_replace(
		array("<br>", "<br />", "\r\n"),
		"\n",
		$description
	);

	// Replace suspiciously-long strings of newlines
	$description = preg_replace(
		"/\n{3,}/",
		"\n\n",
		$description,
	);

	// Turn the newlines into '<br />' tags
	// return nl2br($description);
	return str_replace(
		"\n",
		"<br />",
		$description,
	);
}
