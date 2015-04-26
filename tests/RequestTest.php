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
        $this->setExpectedException('Ardi\Exception\Http\RedirectionException', "Redirecting to $defaultLanguage");
        $this->buildRequestObject()->dispatch();
        $this->assertContains("Location: $defaultLanguage", xdebug_get_headers());
        $this->assertEquals(303, http_response_code());
    }

    public function testUnsupportedLanguage()
    {
        if (!function_exists('http_response_code')) {
            $this->markTestSkipped('PHP 5.4 required to test response code');
        }
        $_REQUEST['lang'] = 'de';
        $this->setExpectedException('Ardi\Exception\Http\BadRequestException', 'Selected language not supported');
        $this->buildRequestObject()->dispatch();
        $this->assertEquals(400, http_response_code());
    }

    public function testNonExistentView()
    {
        if (!function_exists('http_response_code')) {
            $this->markTestSkipped('PHP 5.4 required to test response code');
        }
        $_REQUEST['view'] = $viewName = 'non-existent';
        $_REQUEST['lang'] = $lang = 'en';
        $expectedMessage = "Requested view ($viewName) does not exist for this language ($lang)";
        $this->setExpectedException('Ardi\Exception\Http\NotFoundException', $expectedMessage);
        $this->buildRequestObject()->dispatch();
        $this->assertEquals(404, http_response_code());
    }

    public function testDispatchContactForm()
    {
        if (!function_exists('xdebug_get_headers') || !function_exists('http_response_code')) {
            $this->markTestSkipped('PHP 5.4 with Xdebug is required to test response code and headers');
        }
        $_SERVER['CONTENT_TYPE'] = 'multipart/form-data';
        $_SERVER['HTTP_HOST'] = 'localhost';
        $_SERVER['REQUEST_URI'] = '/en/contact';
        $_POST = array(
            'email' => 'john.doe@localhost',
            'message' => 'Hello!',
            'name' => 'John Doe',
        );
        $this->buildRequestObject()->dispatch();
        $this->assertContains("Location: /en/contact?status=error", xdebug_get_headers());
        $this->assertEquals(303, http_response_code());
    }
}
