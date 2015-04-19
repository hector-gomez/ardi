<?php


use Ardi\Translator;

class TranslatorTest extends PHPUnit_Framework_TestCase
{
    private $langDir = 'tests/fixtures/lang';

    public function testGetCommonString()
    {
        $i18n = new Translator('en', 'common', $this->langDir);
        $this->assertEquals('Test site', $i18n->get('site_name'));
    }

    public function testGetViewSpecificString()
    {
        $i18n = new Translator('en', 'test_view', $this->langDir);
        $this->assertEquals('This is a test description', $i18n->get('meta_description'));
    }

    public function testFallbackToCommonString()
    {
        $msg = 'If the string does not exist in the view section it must search in the "common" section';
        $i18n = new Translator('en', 'test_view', $this->langDir);
        $this->assertEquals('Test site', $i18n->get('site_name'), $msg);
    }

    public function testUnsupportedLanguage()
    {
        $this->setExpectedException('Exception', 'No translation file found for language fr in folder lang');
        new Translator('fr');
    }

    public function testUndefinedString()
    {
        $this->setExpectedException('Exception', 'Could not find a translation for random_string');
        $i18n = new Translator('en', 'test_view', $this->langDir);
        $i18n->get('random_string');
    }

    public function testEmptyString()
    {
        $i18n = new Translator('en', 'test_view', $this->langDir);
        $this->assertEmpty($i18n->get('empty'), 'An empty value for a translation must be respected');
    }
}
