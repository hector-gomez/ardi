<?php


use Ardi\ConfigReader;
use Ardi\Translator;
use Ardi\View;

class ViewTest extends PHPUnit_Framework_TestCase
{
    private $langDir = 'tests/fixtures/lang';
    private $configDir = 'tests/fixtures/config';

    public function setUp()
    {
        ConfigReader::setConfigDir($this->configDir);
        $_SERVER['PHP_SELF'] = '/public/index.php';
    }

    public function testScriptFiles()
    {
        $view = new View('home');
        $view->addScript('script-in-head.js');
        $view->addScript('script-in-body.js', true);
        $view->addScript('http://localhost/external.js', false, true);

        $scriptTagsHead  = "<script src='/static/script-in-head.js'></script>";
        $scriptTagsHead .= "<script src='http://localhost/external.js'></script>";
        $this->assertEquals($scriptTagsHead, $view->getHeadScriptsMarkup());

        $scriptTagsBody = "<script src='/static/script-in-body.js'></script>";
        $this->assertEquals($scriptTagsBody, $view->getBodyScriptsMarkup());
    }

    public function testStylesheets()
    {
        $view = new View('home');
        $view->addStylesheet('style.css');
        $view->addStylesheet('http://localhost/external.css', true);
        $expectedMarkup  = "<link rel='stylesheet' type='text/css' href='/static/style.css'>";
        $expectedMarkup .= "<link rel='stylesheet' type='text/css' href='http://localhost/external.css'>";
        $this->assertEquals($expectedMarkup, $view->getStylesheetsMarkup());
    }

    public function testViewUrlGeneration()
    {
        $view = new View('home');
        $view->lang = 'en';
        $this->assertEquals('/en', $view->getViewUrl('home'));
        $view->lang = 'es';
        $this->assertEquals('/es', $view->getViewUrl('home'));
        $view->lang = 'en';
        $this->assertEquals('/en/contact', $view->getViewUrl('contact'));
        $view->lang = 'es';
        $this->assertEquals('/es/contacto', $view->getViewUrl('contact'));
    }

    public function testTranslator()
    {
        $viewName = 'home';
        $view = new View($viewName);
        $i18n = new Translator('en', $viewName, $this->langDir);
        $view->setTranslator($i18n);
        $this->assertTrue($view->isTranslated());
    }
}
