<?php


use Ardi\ResponseHelper;

/**
 * @runTestsInSeparateProcesses
 */
class ResponseHelperTest extends PHPUnit_Framework_TestCase
{
    public function testCodeForBadRequestError()
    {
        $this->requirePhp54();
        ResponseHelper::issueBadRequestError();
        $this->assertEquals(400, http_response_code());
    }

    public function testHeadersForBadRequestError()
    {
        $this->requireXdebug();
        $defaultMessage = 'Bad Request';
        ResponseHelper::issueBadRequestError();
        $this->assertContains($defaultMessage, xdebug_get_headers());
    }

    public function testCustomMessageForBadRequestError()
    {
        $this->requireXdebug();
        $message = 'There is something wrong in your request';
        ResponseHelper::issueBadRequestError($message);
        $this->assertContains($message, xdebug_get_headers());
    }

    public function testCodeForNotFoundError()
    {
        $this->requirePhp54();
        ResponseHelper::issueNotFoundError();
        $this->assertEquals(404, http_response_code());
    }

    public function testHeadersForNotFoundError()
    {
        $this->requireXdebug();
        $defaultMessage = 'Not found';
        ResponseHelper::issueNotFoundError();
        $this->assertContains($defaultMessage, xdebug_get_headers());
    }

    public function testCustomMessageForNotFoundError()
    {
        $this->requireXdebug();
        $message = 'Could not locate the requested item';
        ResponseHelper::issueNotFoundError($message);
        $this->assertContains($message, xdebug_get_headers());
    }

    public function testRedirectionHeaders()
    {
        $this->requireXdebug();
        $location = 'www.example.com';
        ResponseHelper::redirectTo($location);
        $this->assertContains("Location: $location", xdebug_get_headers());
    }

    public function testDefaultRedirectionCode()
    {
        $this->requirePhp54();
        ResponseHelper::redirectTo('www.example.com');
        $this->assertEquals(303, http_response_code());
    }

    public function testCustomRedirectionCode()
    {
        $this->requirePhp54();
        ResponseHelper::redirectTo('www.example.com', 302);
        $this->assertEquals(302, http_response_code());
    }

    public function testWrongRedirectionCode()
    {
        $code = 500;
        $this->setExpectedException("Exception", "HTTP code $code is not a valid redirection code");
        ResponseHelper::redirectTo('www.example.com', $code);
    }

    private function requirePhp54()
    {
        if (!function_exists('http_response_code')) {
            $this->markTestSkipped('PHP 5.4 or newer is required to test response code');
        }
    }

    private function requireXdebug()
    {
        if (!function_exists('xdebug_get_headers')) {
            $this->markTestSkipped('Xdebug is required to test response headers');
        }
    }
}
