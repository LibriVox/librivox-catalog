# Librivox Tests

We're using [ci-phpunit-test](https://github.com/kenjis/ci-phpunit-test/) to
write PHPUnit tests with.

To get started, you'll need to download a copy of PHPUnit: https://phpunit.de/
I'm using PHPUnit 10 as of writing.

We're using Composer, so follow the instructions to install that and download
everything you need.

From the root of the repo, you should be able to run:

```
./vendor/bin/phpunit -c application/tests/
```

## Coverage

If you want coverage, then you'll need xdebug support. On Ubuntu, you can get
it by installing php-xdebug. The PHPUnit command looks like this:

```
XDEBUG_MODE=coverage ./vendor/bin/phpunit -c application/tests/
```

And the reports will be in `application/tests/build/coverage`.
