# Development environment

The [librivox-ansible] playbooks can be used to set up a local development
server. They come with a [database snapshot] that has been
[scrubbed][database-scrub-script] of all personal information.

Please follow the instructions in that repo for getting set up locally and
configuring your hosts file, and then you can navigate to
https://librivox.org/search to view the catalog. (It uses a self-signed cert
locally, so you should expect a safety warning.)

[librivox-ansible]: https://github.com/LibriVox/librivox-ansible
[database snapshot]: https://github.com/LibriVox/librivox-ansible/blob/master/roles/db_import/files/librivox_catalog_scrubbed.sql.bz2
[database-scrub-script]: https://github.com/LibriVox/librivox-ansible/blob/master/dbscrub.py


# Getting started

If you set up the development environment using the ansible playbooks above,
then all of the code will be living under `/librivox/www/librivox.org/catalog`.

librivox-catalog is a PHP app built on top of [CodeIgniter 3]. If you haven't
used CodeIgniter before, then it's worth starting with these links:

 - [Application Flow Chart](https://codeigniter.com/userguide3/overview/appflow.html)
 - [Tutorial](https://codeigniter.com/userguide3/tutorial/index.html)

They'll give you some idea of how it all fits together and where to find
different bits of code.

[CodeIgniter 3]: https://codeigniter.com/userguide3/general/welcome.html


# Writing tests

For testing, we use [phpunit] with [ci-phpunit-test]. You can find our tests
under [`application/tests`](./application/tests), and there's a README there
that describes how to get started using [composer]. This is the crux of it:

```
$ php composer.phar install
$ XDEBUG_MODE=coverage ./vendor/bin/phpunit -c application/tests/
```

[phpunit]: https://phpunit.de/
[ci-phpunit-test]: https://github.com/kenjis/ci-phpunit-test/
[composer]: https://getcomposer.org/


# Logging in to the admin dashboard locally

All of the tools for managing users, projects, authors, etc. live on the
management dashboards, most of which you need a username/password to log in with
to use. If you're using the default localdev setup, then you can log in using
these:

```
URL: https://librivox.org/workflow
Username: administrator
Password: librivox
```

That password will work for any user, so feel free to log in as whoever you want
really.


# Raising your Pull Request

There aren't any special rules around raising a PR. When you raise it, the
branch builds will try to start up the site and run the tests. You won't have
permission to add any reviewers, but someone will take a look when they have a
moment. Please keep in mind that Librivox doesn't have any full-time devs or
anything, it's just volunteers making whatever time they can, so your patience
and support are appreciated.
