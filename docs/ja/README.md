# CsvView ドキュメント

CsvView は、`JsonView` や `XmlView` が JSON や XML を出力するのと同じように、ビュー変数を CSV として
出力するビュークラスです。モデルのデータを CSV としてエクスポートするのにビューテンプレートが不要になります。

- [インストール](#installation)
- [使い方](#usage)
- [オプション](#options)
- [複雑なモデルデータ](#complex-model-data)
- [拡張子を使う](#using-extensions)
- [ビューテンプレートを使う](#using-view-templates)
- [ダウンロードするファイル名の設定](#setting-the-downloaded-file-name)
- [CsvView コンポーネント](#csvview-component)

<a id="installation"></a>

## インストール

[Composer](https://getcomposer.org/) でプラグインをインストールします。

```bash
composer require pieceofcake2/csv-view
```

このプラグインのタイプは `cakephp-plugin` なので、`composer/installers` によってプラグインディレクトリに
インストールされます。正しい場所にインストールされるように、アプリケーションの `composer.json` で
`installer-paths` を設定してください。

| レイアウト | `installer-paths` |
| --- | --- |
| モダン (`config/`、`src/`、`templates/`、`plugins/`) | `"plugins/{$name}/": ["type:cakephp-plugin"]` |
| レガシー (`app/Config/`、`app/Plugin/`) | `"app/Plugin/{$name}/": ["type:cakephp-plugin"]` |

<a id="enable-the-plugin"></a>

### プラグインの有効化

`config/bootstrap.php` (レガシーレイアウトでは `app/Config/bootstrap.php`) でプラグインを読み込みます。
すでに `CakePlugin::loadAll()` を使っている場合は不要です。

```php
CakePlugin::load('CsvView');
```

<a id="usage"></a>

## 使い方

フラットな配列を CSV としてエクスポートするには、次のようにします。

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

CSV に含める変数は、`JsonView` や `XmlView` とまったく同じように、すべて `$_serialize` ビュー変数に
列挙する必要があります。複数の変数を含めることもできます。

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

ヘッダー行やフッター行を追加するには、`$_header` や `$_footer` ビュー変数を設定します。
どちらも省略できます。

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

<a id="options"></a>

## オプション

区切り文字、囲み文字、改行の置き換え、行末、null の値、バイトオーダーマーク (BOM) は、
`$_delimiter`、`$_enclosure`、`$_newline`、`$_eol`、`$_null`、`$_bom` ビュー変数で設定できます。

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

| 変数 | デフォルト | 説明 |
| --- | --- | --- |
| `_delimiter` | `,` | フィールドの区切り文字 |
| `_enclosure` | `"` | フィールドの囲み文字 |
| `_newline` | `\n` | データ中の改行文字を置き換えます |
| `_eol` | `PHP_EOL` | 各行の行末 |
| `_null` | `NULL` | null の値と存在しないキーに出力する値 |
| `_bom` | `false` | 出力の先頭に UTF-8 のバイトオーダーマークを付けます |
| `_setSeparator` | `false` | 区切り文字を指定した `sep=` 行を 1 行目に追加します |

`_eol` は、出力の各行の終わりに使われます。一方、`_newline` は実際のデータ中の改行文字を置き換えます。
不正な出力にならないように、改行文字は文字列表現 (シングルクォートで囲んだ `'\r\n'`) で指定してください。

[バイトオーダーマーク](https://en.wikipedia.org/wiki/Byte_order_mark) で始まらない UTF-8 のファイルを
正しく表示できないソフトウェアがあります。その場合は `_bom` を `true` にしてください。また、CSV を正しく
表示するのに `_setSeparator` の `sep=` 行が必要なソフトウェアもあります。

<a id="complex-model-data"></a>

## 複雑なモデルデータ

複雑なモデルデータの場合は、`$_extract` ビュー変数で、レコードの中の各カラムのパスを指定します。
これは `Hash::extract()` 互換のパスの配列です。

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

null の値と存在しないキーには、`$_null` の値が書き出されます。

<a id="using-extensions"></a>

## 拡張子を使う

`Router::parseExtensions()` と `RequestHandler` コンポーネントを使うと、`.csv` の URL には自動的に
`CsvView` が使われます。

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

`/posts/export.csv` にアクセスすると、データを CSV として取得できます。

<a id="using-view-templates"></a>

## ビューテンプレートを使う

非常に複雑な CSV には、独自のビューテンプレートを使えます。`$_serialize` を設定しないか、`null` に
してください。ビューテンプレートは、コントローラーのテンプレートの `csv` サブディレクトリに置きます。
たとえば `templates/Posts/csv/export.ctp` (レガシーレイアウトでは `app/View/Posts/csv/export.ctp`)
です。

```php
public function export()
{
    $posts = $this->Post->find('all');
    $_serialize = null;

    $this->viewClass = 'CsvView.Csv';
    $this->set(compact('posts', '_serialize'));
}
```

<a id="setting-the-downloaded-file-name"></a>

## ダウンロードするファイル名の設定

デフォルトでは、ダウンロードされるファイルには URL の最後のセグメントにちなんだ名前が付きます。
たとえば、`example.com/my_controller/my_action` では `my_action.csv` が、
`example.com/my_controller/my_action/first_param` では `first_param.csv` がダウンロードされます。

ファイル名を独自に設定するには、
[`CakeResponse::download()`](https://pieceofcake2.github.io/docs/ja/controllers/request-response.html#文字列をファイルとして送信)
を使います。次の例では、ダウンロードされるファイルが `export.csv` から `my_file.csv` に変わります。

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

<a id="csvview-component"></a>

## CsvView コンポーネント

`CsvView` コンポーネントは、複雑な `find('all')` の結果をエクスポートするのに役立ちます。
ネストした `belongsTo` アソシエーションに対応しています。それ以外のアソシエーション (`hasMany` など) は
無視されます。

コンポーネントを使うには、コントローラーのコンポーネントに追加します。

```php
public $components = ['CsvView.CsvView'];
```

<a id="prepareextractfromfindresults"></a>

### prepareExtractFromFindResults()

`prepareExtractFromFindResults($data, $excludePaths = [])`

`$data` を再帰的に調べ、`$_extract` 変数に使える、`Hash::extract()` 互換の一意なパスをすべて返します。

* `$data`: `find('all')` の結果。
* `$excludePaths` (省略可): 返される配列から除外するパス。`Hash::extract()` 互換の構文で、
  たとえば `['MyModel.column_name']` のように指定します。

<a id="prepareheaderfromextract"></a>

### prepareHeaderFromExtract()

`prepareHeaderFromExtract($extract, $customHeaders = [], $options = [])`

`$extract` のパスをもとに、`$_header` 変数に使える、わかりやすいカラムのタイトルを返します。
たとえば、パス `City.Country.name` は `Country Name` になります。

* `$extract`: `Hash::extract()` 互換の構文のパス。
* `$customHeaders` (省略可): `'path' => 'Custom Title'` の組。たとえば
  `['City.population' => 'No. of People']` です。生成されたタイトルを上書きします。
* `$options` (省略可):
  * `includeClassname` (デフォルト `true`): 生成されるタイトルにモデル名を含めます。
  * `humanReadable` (デフォルト `true`): アンダースコアをスペースに置き換え、各単語の先頭の文字を
    大文字にします。

<a id="quickexport"></a>

### quickExport()

`quickExport($data, $excludePaths = [], $customHeaders = [], $options = [])`

`find('all')` の結果を 1 行のコードでエクスポートします。

* `$data`: `find('all')` の結果。
* `$excludePaths` (省略可): [`prepareExtractFromFindResults()`](#prepareextractfromfindresults) と同じです。
* `$customHeaders` (省略可): [`prepareHeaderFromExtract()`](#prepareheaderfromextract) と同じです。
* `$options` (省略可): `prepareHeaderFromExtract()` のオプションと、ヘッダー行を含める `includeHeader`
  (デフォルト `true`)。後方互換性のため、真偽値も `includeHeader` として受け付けます。

最もシンプルな使い方:

```php
$results = $this->MyModel->find('all');
$this->CsvView->quickExport($results);
```

高度な使い方:

```php
$results = $this->MyModel->find('all');
$excludePaths = ['City.id', 'State.id', 'State.Country.id']; // Exclude all id fields
$customHeaders = ['City.population' => 'No. of People'];

$this->CsvView->quickExport($results, $excludePaths, $customHeaders);
```

`quickExport()` を使わない場合:

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

<a id="origin"></a>

## 由来

このパッケージは
[FriendsOfCake/cakephp-csvview](https://github.com/FriendsOfCake/cakephp-csvview)
(Packagist では `friendsofcake/cakephp-csvview`、アップストリームの最終バージョンは 1.3.0)
のメンテナンスされたフォークで、元は Jose Diaz-Gonzalez によって作られました。
[pieceofcake2](https://github.com/pieceofcake2) が、モダンな PHP 上の CakePHP 2 向けにメンテナンスしています。
オリジナルからの変更点は
[フォーク以降のコミット履歴](https://github.com/pieceofcake2/csv-view/compare/7afe84f...2.x)
にあります。
