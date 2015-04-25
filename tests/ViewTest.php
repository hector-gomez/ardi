<?php


use Ardi\ConfigReader;
use Ardi\Translator;
use Ardi\View;

class ViewTest extends PHPUnit_Framework_TestCase
{
    private $configDir = 'tests/fixtures/config';
    private $langDir = 'tests/fixtures/lang';
    private $layoutsDir = 'tests/fixtures/layouts';
    private $viewsDir = 'tests/fixtures/views';

    private function getExpectedOutputFor($functionName)
    {
        return file_get_contents("tests/resources/ViewTest/$functionName.html");
    }

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

    public function testRenderingOutput()
    {
        $view = new View('home', 'simple');
        $view->setViewsDir($this->viewsDir);
        $view->setLayoutsDir($this->layoutsDir);
        $expectedOutput = $this->getExpectedOutputFor('testRenderingOutput');
        $this->expectOutputString($expectedOutput);
        $this->assertEquals($expectedOutput, $view->render());
    }

    public function testRenderingWithVariableInjection()
    {
        // Values to inject in the view
        $description = 'This is the site home page';
        $keywords = 'list,of,keywords';
        $lang = 'en';
        $title = 'Homepage';

        // Construct the view object
        $view = new View('home');
        $view->setViewsDir($this->viewsDir);
        $view->setLayoutsDir($this->layoutsDir);
        $view->description = $description;
        $view->keywords = $keywords;
        $view->lang = $lang;
        $view->title = $title;

        // Parse the result of rendering the view as DOM
        $document = new DOMDocument();
        $document->loadHTML($view->render(false));

        $titleNode = $document->getElementsByTagName('title')->item(0);
        $this->assertEquals($title, $titleNode->nodeValue);

        $htmlNode =  $document->getElementsByTagName('html')->item(0);
        $this->assertEquals($lang, $htmlNode->attributes->getNamedItem('lang')->nodeValue);

        // For this test the order of the meta tags in default.phtml must be kept
        $metaNodes = $document->getElementsByTagName('meta');
        $this->assertEquals('utf-8', $metaNodes->item(0)->attributes->getNamedItem('charset')->nodeValue);
        $this->assertEquals($keywords, $metaNodes->item(1)->attributes->getNamedItem('content')->nodeValue);
        $this->assertEquals($description, $metaNodes->item(2)->attributes->getNamedItem('content')->nodeValue);
    }
}
