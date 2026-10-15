# CsvView Documentation

CsvView is a view class that renders view variables as CSV, like `JsonView` and `XmlView`
do for JSON and XML. Exporting model data as CSV then needs no view template.

- [Installation](#installation)
- [Usage](#usage)
- [Options](#options)
- [Complex Model Data](#complex-model-data)
- [Using Extensions](#using-extensions)
- [Using View Templates](#using-view-templates)
- [Setting the Downloaded File Name](#setting-the-downloaded-file-name)
- [CsvView Component](#csvview-component)

## Installation

Install the plugin with [Composer](https://getcomposer.org/):

```bash
composer require pieceofcake2/csv-view
```

The plugin has the `cakephp-plugin` type, so it is installed into your plugins directory
by `composer/installers`. Set `installer-paths` in your application's `composer.json` so it
is installed into the right place:

| Layout | `installer-paths` |
| --- | --- |
| Modern (`config/`, `src/`, `templates/`, `plugins/`) | `"plugins/{$name}/": ["type:cakephp-plugin"]` |
| Legacy (`app/Config/`, `app/Plugin/`) | `"app/Plugin/{$name}/": ["type:cakephp-plugin"]` |

### Enable the plugin

Load the plugin in `config/bootstrap.php` (legacy layout: `app/Config/bootstrap.php`).
If you already use `CakePlugin::loadAll()`, this is not necessary:

```php
CakePlugin::load('CsvView');
```

## Usage

To export a flat array as CSV:

```php
public function export()
{
    $data = [
        ['a', 'b', 'c'],
        [1, 2, 3],
        ['you', 'and', 'me'],
    ];
    $_serialize = 'data';

    $this->viewClass = 'CsvView.Csv';
    $this->set(compact('data', '_serialize'));
}
```

All variables to be included in the CSV must be listed in the `$_serialize` view variable,
exactly as with `JsonView` or `XmlView`. You can include multiple variables:

```php
public function export()
{
    $data = [['a', 'b', 'c']];
    $dataTwo = [[1, 2, 3]];
    $dataThree = [['you', 'and', 'me']];

    $_serialize = ['data', 'dataTwo', 'dataThree'];

    $this->viewClass = 'CsvView.Csv';
    $this->set(compact('data', 'dataTwo', 'dataThree', '_serialize'));
}
```

To add a header or a footer row, set the `$_header` or `$_footer` view variable. Both are
optional:

```php
public function export()
{
    $data = [
        ['a', 'b', 'c'],
        [1, 2, 3],
        ['you', 'and', 'me'],
    ];

    $_serialize = 'data';
    $_header = ['Column 1', 'Column 2', 'Column 3'];
    $_footer = ['Totals', '400', '$3000'];

    $this->viewClass = 'CsvView.Csv';
    $this->set(compact('data', '_serialize', '_header', '_footer'));
}
```

## Options

You can set the delimiter, enclosure, newline replacement, end of line, null value and byte
order mark (BOM) with the `$_delimiter`, `$_enclosure`, `$_newline`, `$_eol`, `$_null` and
`$_bom` view variables:

```php
public function export()
{
    $data = [
        ['a', 'b', 'c'],
        [1, 2, 3],
        ['you', 'and', 'me'],
    ];

    $_serialize = 'data';
    $_delimiter = chr(9); // Tab
    $_enclosure = '"';
    $_newline = '\r\n';
    $_eol = '~';
    $_bom = true;

    $this->viewClass = 'CsvView.Csv';
    $this->set(compact('data', '_serialize', '_delimiter', '_enclosure', '_newline', '_eol', '_bom'));
}
```

| Variable | Default | Description |
| --- | --- | --- |
| `_delimiter` | `,` | Field delimiter |
| `_enclosure` | `"` | Field enclosure |
| `_newline` | `\n` | Replaces newline characters in the data |
| `_eol` | `PHP_EOL` | End of line of each row |
| `_null` | `NULL` | Output for null values and missing keys |
| `_bom` | `false` | Adds a UTF-8 byte order mark at the beginning of the output |
| `_setSeparator` | `false` | Adds a `sep=` line with the delimiter as the first line |

`_eol` is used to end each row of the output. `_newline`, however, replaces the newline
characters in the actual data. Use the string representation of the newline character
(`'\r\n'` in single quotes) to avoid rendering invalid output.

Some software renders UTF-8 files incorrectly when they do not start with a
[byte order mark](https://en.wikipedia.org/wiki/Byte_order_mark); set `_bom` to `true` for
them. Some readers also need the `sep=` line of `_setSeparator` to display the CSV
correctly.

## Complex Model Data

For complex model data, use the `$_extract` view variable to set the path of each column in
a record. It is an array of `Hash::extract()` compatible paths:

```php
public function export()
{
    $posts = $this->Post->find('all');
    $_serialize = 'posts';
    $_header = ['Post ID', 'Title', 'Created'];
    $_extract = ['Post.id', 'Post.title', 'Post.created'];

    $this->viewClass = 'CsvView.Csv';
    $this->set(compact('posts', '_serialize', '_header', '_extract'));
}
```

Null values and missing keys are written as the value of `$_null`.

## Using Extensions

With `Router::parseExtensions()` and the `RequestHandler` component, `CsvView` is used
automatically for `.csv` URLs:

```php
// In config/routes.php (legacy layout: app/Config/routes.php)
Router::parseExtensions('csv');
```

```php
// In your controller
public $components = [
    'RequestHandler' => [
        'viewClassMap' => ['csv' => 'CsvView.Csv'],
    ],
];

public function export()
{
    $posts = $this->Post->find('all');
    $_serialize = 'posts';
    $_header = ['Post ID', 'Title', 'Created'];
    $_extract = ['Post.id', 'Post.title', 'Post.created'];

    $this->set(compact('posts', '_serialize', '_header', '_extract'));
}
```

Access `/posts/export.csv` to get the data as CSV.

## Using View Templates

For really complex CSVs, you can use your own view templates. Leave `$_serialize` unset or
set it to `null`. The view templates are in the `csv` subdirectory of the controller's
templates, e.g. `templates/Posts/csv/export.ctp` (legacy layout:
`app/View/Posts/csv/export.ctp`):

```php
public function export()
{
    $posts = $this->Post->find('all');
    $_serialize = null;

    $this->viewClass = 'CsvView.Csv';
    $this->set(compact('posts', '_serialize'));
}
```

## Setting the Downloaded File Name

By default, the downloaded file is named after the last segment of the URL. For example,
`example.com/my_controller/my_action` downloads `my_action.csv`, while
`example.com/my_controller/my_action/first_param` downloads `first_param.csv`.

To set a custom file name, use
[`CakeResponse::download()`](https://pieceofcake2.github.io/docs/controllers/request-response.html#sending-a-string-as-file).
The following changes the downloaded file from `export.csv` to `my_file.csv`:

```php
public function export()
{
    $data = [
        ['a', 'b', 'c'],
        [1, 2, 3],
        ['you', 'and', 'me'],
    ];
    $_serialize = 'data';

    $this->response->download('my_file.csv');
    $this->viewClass = 'CsvView.Csv';
    $this->set(compact('data', '_serialize'));
}
```

## CsvView Component

The `CsvView` component helps you export the results of complex `find('all')` calls.
Nested `belongsTo` associations are supported; other associations (e.g. `hasMany`) are
ignored.

To use the component, add it to your controller's components:

```php
public $components = ['CsvView.CsvView'];
```

### prepareExtractFromFindResults()

`prepareExtractFromFindResults($data, $excludePaths = [])`

Recursively searches `$data` and returns all unique `Hash::extract()` compatible paths,
suitable for the `$_extract` variable.

* `$data`: the results of a `find('all')` call.
* `$excludePaths` (optional): paths to exclude from the returned array, in
  `Hash::extract()` compatible syntax, e.g. `['MyModel.column_name']`.

### prepareHeaderFromExtract()

`prepareHeaderFromExtract($extract, $customHeaders = [], $options = [])`

Returns user-friendly column titles, suitable for the `$_header` variable, based on the paths
in `$extract`. For example, the path `City.Country.name` becomes `Country Name`.

* `$extract`: paths in `Hash::extract()` compatible syntax.
* `$customHeaders` (optional): `'path' => 'Custom Title'` pairs, e.g.
  `['City.population' => 'No. of People']`. They override the generated titles.
* `$options` (optional):
  * `includeClassname` (default `true`): include the model name in the generated titles.
  * `humanReadable` (default `true`): replace underscores with spaces and uppercase the
    first character of each word.

### quickExport()

`quickExport($data, $excludePaths = [], $customHeaders = [], $options = [])`

Exports the results of a `find('all')` call in one line of code.

* `$data`: the results of a `find('all')` call.
* `$excludePaths` (optional): as in [`prepareExtractFromFindResults()`](#prepareextractfromfindresults).
* `$customHeaders` (optional): as in [`prepareHeaderFromExtract()`](#prepareheaderfromextract).
* `$options` (optional): the options of `prepareHeaderFromExtract()`, and `includeHeader`
  (default `true`) to include a header row. A boolean is accepted as `includeHeader` for
  backward compatibility.

Simplest use:

```php
$results = $this->MyModel->find('all');
$this->CsvView->quickExport($results);
```

Advanced use:

```php
$results = $this->MyModel->find('all');
$excludePaths = ['City.id', 'State.id', 'State.Country.id']; // Exclude all id fields
$customHeaders = ['City.population' => 'No. of People'];

$this->CsvView->quickExport($results, $excludePaths, $customHeaders);
```

Without `quickExport()`:

```php
$results = $this->MyModel->find('all');

$excludePaths = ['City.id', 'State.id', 'State.Country.id']; // Exclude all id fields
$_extract = $this->CsvView->prepareExtractFromFindResults($results, $excludePaths);

$customHeaders = ['City.population' => 'No. of People'];
$_header = $this->CsvView->prepareHeaderFromExtract($_extract, $customHeaders);

$_serialize = 'results';
$this->viewClass = 'CsvView.Csv';
$this->set(compact('results', '_serialize', '_header', '_extract'));
```
