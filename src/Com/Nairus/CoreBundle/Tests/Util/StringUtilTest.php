<?php

namespace Com\Nairus\CoreBundle\Util;

use PHPUnit\Framework\TestCase;

/**
 * Test of StringUtil.
 *
 * @author nairus <nicolas.surian@gmail.com>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class StringUtilTest extends TestCase {

    /**
     * Test if the constructor has private scope and it has to throw an error.
     *
     * @expectedException \Error
     *
     * @return void1
     */
    public function testConstructor(): void {
        new StringUtil();
    }

    /**
     * Test the <code>camelize</code> method.
     *
     * @return void
     */
    public function testCamelize(): void {
        $tests = [
            "camel12_case34" => "Camel12Case34",
            "bad camel_case" => "BadcamelCase",
            "foo-bar" => "Foobar",
        ];
        foreach ($tests as $test => $result) {
            $this->assertEquals($result, StringUtil::camelize($test), "1. The string expected id not ok.");
        }
    }

    /**
     * Test the <code>decamelize</code> method.
     *
     * @return void
     */
    public function testDecamelize(): void {
        $tests = [
            'simpleTest' => 'simple_test',
            'easy' => 'easy',
            'HTML' => 'html',
            'simpleXML' => 'simple_xml',
            'PDFLoad' => 'pdf_load',
            'startMIDDLELast' => 'start_middle_last',
            'AString' => 'a_string',
            'Some4Numbers234' => 'some4_numbers234',
            'TEST123String' => 'test123_string',
            'hello_world' => 'hello_world',
            'hello___world' => 'hello___world',
            '_hello_world_' => '_hello_world_',
            'HelloWorld' => 'hello_world',
            'helloWorldFoo' => 'hello_world_foo',
            'hello_World' => 'hello_world',
            'hello-world' => 'hello-world',
            'myHTMLFiLe' => 'my_html_fi_le',
            'aBaBaB' => 'a_ba_ba_b',
            'BaBaBa' => 'ba_ba_ba',
            'libC' => 'lib_c',
        ];

        foreach ($tests as $test => $result) {
            $output = StringUtil::decamelize($test);
            $this->assertEquals($result, $output);
        }
    }

}
