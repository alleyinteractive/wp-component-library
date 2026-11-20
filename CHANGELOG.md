# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## Unreleased

## v1.3.0

- Replace `paquettg/php-html-parser`, which is unmaintained and held `guzzlehttp/guzzle` and `guzzlehttp/psr7` on versions with security advisories, with an `Html_Node` class built on PHP's DOM extension. Block attribute values read from markup are now entity-decoded (`&amp;` becomes `&`), so templates should keep escaping them on output.
- Update all Composer dependencies; `composer audit` reports no advisories.

## v1.2.1

- Fix `optional()` references previously supplied by `illuminate/support`.

## v1.2.0

- Remove illuminate/support dependency.
- Dependency update, deprecation notice.

## v1.1.1

Fix PHP warning.

## v1.1.0

PHP 8.3 support

## v1.0.1

Bump illuminate/support package.

## v1.0.0

- Initial release.
