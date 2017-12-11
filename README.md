# LibriVox catalog and reader workflow application

This is the LibriVox catalog and reader workflow application. It powers the
catalog (for example [author
search](https://librivox.org/search?primary_key=0&search_category=author&search_page=1&search_form=get_results))
and the private web application used by the volunteers to produce the
audiobooks. The former is pretty tightly integrated into a WordPress blog by
means of an [Apache
configuration](https://github.com/LibriVox/librivox-ansible/blob/master/roles/blog%2Bcatalog/templates/librivox.org.conf)
and a [WordPress theme](https://github.com/LibriVox/librivox-wordpress-theme).
The blog+catalog combination is the LibriVox [home
page](https://librivox.org/).
