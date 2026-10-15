# CsvView Plugin

[![GitHub License](https://img.shields.io/github/license/pieceofcake2/csv-view?label=License)](LICENSE)
[![Packagist Version](https://img.shields.io/packagist/v/pieceofcake2/csv-view?label=Packagist)](https://packagist.org/packages/pieceofcake2/csv-view)
![PHP](https://img.shields.io/packagist/dependency-v/pieceofcake2/csv-view/php?logo=php&logoColor=%23FFFFFF&label=PHP&labelColor=%23777BB4&color=%23FFFFFF)
[![PHPStan](https://img.shields.io/badge/PHPStan-level%205-brightgreen?label=PHPStan)](https://github.com/pieceofcake2/csv-view/actions/workflows/CI.yml)
![CakePHP](https://img.shields.io/packagist/dependency-v/pieceofcake2/csv-view/pieceofcake2/cakephp?logo=cakephp&logoColor=%23FFFFFF&label=CakePHP&labelColor=%23D33C43&color=%23FFFFFF)
[![CI](https://img.shields.io/github/actions/workflow/status/pieceofcake2/csv-view/CI.yml?label=CI)](https://github.com/pieceofcake2/csv-view/actions/workflows/CI.yml)
[![Codecov](https://img.shields.io/codecov/c/gh/pieceofcake2/csv-view?label=Coverage)](https://codecov.io/gh/pieceofcake2/csv-view)

CsvView renders view variables as CSV, like `JsonView` and `XmlView` do for JSON and XML, so
exporting model data as CSV needs no view template. The `CsvView` component builds the columns
and headers from `find('all')` results. This is a fork of
[FriendsOfCake/cakephp-csvview](https://github.com/FriendsOfCake/cakephp-csvview) 2.x for
[pieceofcake2/cakephp](https://github.com/pieceofcake2/cakephp).

## Documentation

- Website: https://pieceofcake2.github.io/csv-view/
- Markdown: [docs/README.md](docs/README.md) (for AI tools and offline reading)

The Markdown documentation is also included in the Composer package, in the plugin's directory: `plugins/CsvView/docs/README.md` (the location depends on your `installer-paths`). Install the package with `--dev` if you only need it for development.

## Requirements

* PHP 8.0+
* pieceofcake2/cakephp 2.12+

## Installation

```
composer require pieceofcake2/csv-view
```

## Contributing

See the [contribution guidelines](https://github.com/pieceofcake2/.github/blob/master/CONTRIBUTING.md).

## License

The MIT License. See [LICENSE](LICENSE).
