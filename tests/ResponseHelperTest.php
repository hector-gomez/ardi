<?php


use Ardi\ResponseHelper;

/**
 * @runTestsInSeparateProcesses
 */
class ResponseHelperTest extends PHPUnit_Framework_TestCase
{
    /**
     * @requires PHP 5.4
     */
    public function testCodeForBadRequestError()
    {
        ResponseHelper::issueBadRequestError();
        $this->assertEquals(400, http_response_code());
    }

    /**
     * @requires extension xdebug
     */
    public function testHeadersForBadRequestError()
    {
        $defaultMessage = 'Bad Request';
        ResponseHelper::issueBadRequestError();
        $this->assertContains($defaultMessage, xdebug_get_headers());
    }

    /**
     * @requires extension xdebug
     */
    public function testCustomMessageForBadRequestError()
    {
        $message = 'There is something wrong in your request';
        ResponseHelper::issueBadRequestError($message);
        $this->assertContains($message, xdebug_get_headers());
    }

    /**
     * @requires PHP 5.4
     */
    public function testCodeForNotFoundError()
    {
        ResponseHelper::issueNotFoundError();
        $this->assertEquals(404, http_response_code());
    }

    /**
     * @requires extension xdebug
     */
    public function testHeadersForNotFoundError()
    {
        $defaultMessage = 'Not found';
        ResponseHelper::issueNotFoundError();
        $this->assertContains($defaultMessage, xdebug_get_headers());
    }

    /**
     * @requires extension xdebug
     */
    public function testCustomMessageForNotFoundError()
    {
        $message = 'Could not locate the requested item';
        ResponseHelper::issueNotFoundError($message);
        $this->assertContains($message, xdebug_get_headers());
    }

    /**
     * @requires extension xdebug
     */
    public function testRedirectionHeaders()
    {
        $location = 'www.example.com';
        ResponseHelper::redirectTo($location);
        $this->assertContains("Location: $location", xdebug_get_headers());
    }

    /**
     * @requires PHP 5.4
     */
    public function testDefaultRedirectionCode()
    {
        ResponseHelper::redirectTo('www.example.com');
        $this->assertEquals(303, http_response_code());
    }

    /**
     * @requires PHP 5.4
     */
    public function testCustomRedirectionCode()
    {
        ResponseHelper::redirectTo('www.example.com', 302);
        $this->assertEquals(302, http_response_code());
    }

    public function testWrongRedirectionCode()
    {
        $code = 500;
        $this->setExpectedException("Exception", "HTTP code $code is not a valid redirection code");
        ResponseHelper::redirectTo('www.example.com', $code);
    }
}
