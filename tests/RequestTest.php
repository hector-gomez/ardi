<?php


use Ardi\ConfigReader;
use Ardi\Request;

/**
 * @runTestsInSeparateProcesses
 */
class RequestTest extends PHPUnit_Framework_TestCase
{
    private $configDir = 'tests/fixtures/config';
    private $langDir = 'tests/fixtures/lang';
    private $layoutsDir = 'tests/fixtures/layouts';
    private $viewsDir = 'tests/fixtures/views';

    public function setUp()
    {
        ConfigReader::setConfigDir($this->configDir);
        $_SERVER['PHP_SELF'] = '/public/index.php';
        $_SERVER['CONTENT_TYPE'] = 'text/plain';
        $_REQUEST = array(
            'lang' => 'en',
            'view' => '',
        );
    }

    private function buildRequestObject()
    {
        $r = Request::getInstance();
        $r->setLangDir($this->langDir);
        $r->setLayoutsDir($this->layoutsDir);
        $r->setViewsDir($this->viewsDir);
        return $r;
    }

    public function testDispatch()
    {
        $r = $this->buildRequestObject();
        $actual = new DOMDocument();
        $actual->loadHTML($r->dispatch(false));
        $expected = new DOMDocument();
        $expected->loadHTMLFile('tests/resources/RequestTest/testDispatch.html');
        $this->assertEqualXMLStructure($expected->documentElement, $actual->documentElement);
    }

    public function testParameters()
    {
        $_GET = array(
            'one' => 'firstQueryParameter',
            'two' => 'secondQueryParameter',
        );
        $r = Request::getInstance();
        $this->assertEquals('firstQueryParameter', $r->getQueryParam('one'));
        $this->assertEquals('secondQueryParameter', $r->getQueryParam('two'));
        $this->assertEquals('en', $r->getRouteParam('lang'));
    }

    public function testRedirectToDefaultLanguage()
    {
        if (!function_exists('xdebug_get_headers') || !function_exists('http_response_code')) {
            $this->markTestSkipped('PHP 5.4 with Xdebug is required to test response code and headers');
        }
        $_REQUEST['lang'] = '';
        $appConfig = ConfigReader::getReader('app');
        $defaultLanguage = $appConfig->get('languages.default');
        $request = $this->buildRequestObject();
        $request->dispatch();
        $this->assertContains("Location: $defaultLanguage", xdebug_get_headers());
        $this->assertEquals(303, http_response_code());
    }

    public function testUnsupportedLanguage()
    {
        if (!function_exists('http_response_code')) {
            $this->markTestSkipped('PHP 5.4 required to test response code');
        }
        $_REQUEST['lang'] = 'de';
        $this->expectOutputString('Selected language not supported');
        $request = $this->buildRequestObject();
        $request->dispatch();
        $this->assertEquals(400, http_response_code());
    }

    public function testNonExistentView()
    {
        if (!function_exists('http_response_code')) {
            $this->markTestSkipped('PHP 5.4 required to test response code');
        }
        $_REQUEST['view'] = $viewName = 'non-existent';
        $_REQUEST['lang'] = $lang = 'en';
        $this->expectOutputString("Requested view ($viewName) does not exist for this language ($lang)");
        $request = $this->buildRequestObject();
        $request->dispatch();
        $this->assertEquals(404, http_response_code());
    }

    //TODO Missing testDispatchContactForm
}
