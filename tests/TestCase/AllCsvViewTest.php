<?php
/**
 * All CsvView plugin tests
 */

class AllCsvViewTest extends CakeTestCase
{
    /**
     * Suite define the tests for this suite
     *
     * @return CakeTestSuite
     */
    public static function suite(): CakeTestSuite
    {
        $suite = new CakeTestSuite('All CsvView test');

        $path = CakePlugin::path('CsvView') . 'tests' . DS . 'TestCase' . DS;
        $suite->addTestDirectoryRecursive($path);

        return $suite;
    }
}
