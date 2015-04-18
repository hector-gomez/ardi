<?php


use Ardi\Utils;

class UtilsTest extends PHPUnit_Framework_TestCase
{
    public function testTags()
    {
        $original = 'This <b>text</b> includes <strong>tags</strong>';
        $expected = 'This text includes tags';
        $this->assertEquals($expected, Utils::sanitizeString($original), 'Tags must be stripped out');
    }

    public function testQuotes()
    {
        $original = 'This \'text\' includes "quotes"';
        $expected = 'This &#039;text&#039; includes &quot;quotes&quot;';
        $this->assertEquals($expected, Utils::sanitizeString($original), 'Quotes must be converted into entities');
    }
}
