<?php
/**
 * CakePHP(tm) Tests <https://book.cakephp.org/2.0/en/development/testing.html>
 * Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice
 *
 * @copyright     Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          https://book.cakephp.org/2.0/en/development/testing.html CakePHP(tm) Tests
 * @since         CakePHP(tm) v 2.1.0
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

App::uses('CsvView', 'CsvView.View');

/**
 * CsvViewTest
 */
class CsvViewTest extends CakeTestCase
{
    /**
     * testRenderWithoutView method
     *
     * @return void
     */
    public function testRenderWithoutView(): void
    {
        $Request = new CakeRequest();
        $Response = new CakeResponse();
        $Controller = new Controller($Request, $Response);
        $data = [['user', 'fake', 'list', 'item1', 'item2']];
        $Controller->set(['data' => $data, '_serialize' => 'data']);
        $View = new CsvView($Controller);
        $output = $View->render(false);

        $this->assertSame('user,fake,list,item1,item2' . PHP_EOL, $output);
        $this->assertSame('text/csv', $Response->type());
    }

    /**
     * Test render with an array in _serialize
     *
     * @return void
     */
    public function testRenderWithoutViewMultiple(): void
    {
        $Request = new CakeRequest();
        $Response = new CakeResponse();
        $controller = new Controller($Request, $Response);
        $data = [
            ['a', 'b', 'c'],
            [1, 2, 3],
            ['you', 'and', 'me'],
        ];
        $controller->set('data', $data);
        $controller->set(['_serialize' => 'data']);
        $View = new CsvView($controller);
        $output = $View->render(false);

        $this->assertSame('a,b,c' . PHP_EOL . '1,2,3' . PHP_EOL . 'you,and,me' . PHP_EOL, $output);
        $this->assertSame('text/csv', $Response->type());
    }

    /**
     * Test render with a custom EOL char.
     *
     * @return void
     */
    public function testRenderWithCustomEol(): void
    {
        $Request = new CakeRequest();
        $Response = new CakeResponse();
        $controller = new Controller($Request, $Response);
        $data = [
            ['a', 'b', 'c'],
            [1, 2, 3],
            ['you', 'and', 'me'],
        ];
        $controller->set('data', $data);
        $controller->set(['_serialize' => 'data']);
        $View = new CsvView($controller);
        $View->viewVars['_eol'] = '~';
        $output = $View->render(false);

        $this->assertSame('a,b,c~1,2,3~you,and,me~', $output);
        $this->assertSame('text/csv', $Response->type());
    }

    /**
     * testRenderWithView method
     *
     * @return void
     */
    public function testRenderWithView(): void
    {
        App::build([
            'View' => realpath(dirname(__FILE__) . DS . '..' . DS . '..' . DS . 'test_app' . DS . 'View' . DS) . DS,
        ]);
        $Request = new CakeRequest();
        $Response = new CakeResponse();
        $Controller = new Controller($Request, $Response);
        $Controller->name = $Controller->viewPath = 'Posts';

        $data = [
            ['a', 'b', 'c'],
            [1, 2, 3],
            ['you', 'and', 'me'],
        ];

        $Controller->set('user', $data);
        $View = new CsvView($Controller);
        $output = $View->render('index');

        $this->assertSame('TEST OUTPUT' . PHP_EOL, $output);
        $this->assertSame('text/csv', $Response->type());
    }

    /**
     * CsvViewTest::testRenderViaExtract()
     *
     * @return void
     */
    public function testRenderViaExtract(): void
    {
        App::build([
            'View' => realpath(dirname(__FILE__) . DS . '..' . DS . '..' . DS . 'test_app' . DS . 'View' . DS) . DS,
        ]);
        $Request = new CakeRequest();
        $Response = new CakeResponse();
        $Controller = new Controller($Request, $Response);
        $Controller->name = $Controller->viewPath = 'Posts';

        $data = [
            [
                'User' => [
                    'username' => 'jose',
                ],
                'Item' => [
                    'name' => 'beach',
                ],
            ],
            [
                'User' => [
                    'username' => 'drew',
                ],
                'Item' => [
                    'name' => 'ball',
                ],
            ],
        ];
        $_extract = ['User.username', 'Item.name'];
        $Controller->set(['user' => $data, '_extract' => $_extract]);
        $Controller->set(['_serialize' => 'user']);
        $View = new CsvView($Controller);
        $output = $View->render(false);

        $this->assertSame('jose,beach' . PHP_EOL . 'drew,ball' . PHP_EOL, $output);
        $this->assertSame('text/csv', $Response->type());
    }

    /**
     * CsvViewTest::testRenderViaExtractOptionalField()
     *
     * @return void
     */
    public function testRenderViaExtractOptionalField(): void
    {
        App::build([
            'View' => realpath(dirname(__FILE__) . DS . '..' . DS . '..' . DS . 'test_app' . DS . 'View' . DS) . DS,
        ]);
        $Request = new CakeRequest();
        $Response = new CakeResponse();
        $Controller = new Controller($Request, $Response);
        $Controller->name = $Controller->viewPath = 'Posts';

        $data = [
            [
                'User' => [
                    'username' => 'jose',
                ],
                'Item' => [
                    'type' => 'beach',
                ],
            ],
            [
                'User' => [
                    'username' => 'drew',
                ],
                'Item' => [
                    'name' => 'ball',
                    'type' => 'fun',
                ],
            ],
        ];
        $_extract = ['User.username', 'Item.name', 'Item.type'];
        $Controller->set(['user' => $data, '_extract' => $_extract]);
        $Controller->set(['_serialize' => 'user']);
        $View = new CsvView($Controller);
        $output = $View->render(false);

        $this->assertSame('jose,NULL,beach' . PHP_EOL . 'drew,ball,fun' . PHP_EOL, $output);
        $this->assertSame('text/csv', $Response->type());
    }

    /**
     * CsvViewTest::testRenderWithSpecialCharacters()
     *
     * @return void
     */
    public function testRenderWithSpecialCharacters(): void
    {
        App::build([
            'View' => dirname(__FILE__, 3) . DS . 'test_app' . DS . 'View' . DS,
        ]);
        $Request = new CakeRequest();
        $Response = new CakeResponse();
        $Controller = new Controller($Request, $Response);
        $Controller->name = $Controller->viewPath = 'Posts';

        $data = [
            [
                'User' => [
                    'username' => 'José',
                ],
                'Item' => [
                    'type' => 'äöü',
                ],
            ],
            [
                'User' => [
                    'username' => 'Including,Comma',
                ],
                'Item' => [
                    'name' => 'Containing"char',
                    'type' => 'Containing\'char',
                ],
            ],
            [
                'User' => [
                    'username' => 'Some Space',
                ],
                'Item' => [
                    'name' => "A\nNewline",
                    'type' => "A\tTab",
                ],
            ],
        ];
        $_extract = ['User.username', 'Item.name', 'Item.type'];
        $Controller->set(['user' => $data, '_extract' => $_extract]);
        $Controller->set(['_serialize' => 'user']);
        $View = new CsvView($Controller);
        $output = $View->render(false);

        $expected = <<<CSV
José,NULL,äöü
"Including,Comma","Containing""char",Containing'char
"Some Space","A
Newline","A\tTab"

CSV;
        $this->assertTextEquals($expected, $output);
        $this->assertSame('text/csv', $Response->type());
    }
}
