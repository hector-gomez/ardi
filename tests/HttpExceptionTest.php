<?php


use Ardi\Exception\Http\BadRequestException;
use Ardi\Exception\Http\NotFoundException;
use Ardi\Exception\Http\RedirectionException;

/**
 * @runTestsInSeparateProcesses
 */
class HttpExceptionTest extends PHPUnit_Framework_TestCase
{
    public function testBadRequestException()
    {
        $this->setExpectedException('Ardi\Exception\Http\BadRequestException');
        throw new BadRequestException();
    }

    public function testBadRequestExceptionWithCustomMessage()
    {
        $message = 'Bad request';
        $this->setExpectedException('Ardi\Exception\Http\BadRequestException', $message);
        throw new BadRequestException($message);
    }

    public function testNotFoundException()
    {
        $this->setExpectedException('Ardi\Exception\Http\NotFoundException');
        throw new NotFoundException();
    }

    public function testNotFoundExceptionWithCustomMessage()
    {
        $message = 'File not found';
        $this->setExpectedException('Ardi\Exception\Http\NotFoundException', $message);
        throw new NotFoundException($message);
    }

    public function testRedirectionException()
    {
        $url = 'http://www.example.com';
        $this->setExpectedException('Ardi\Exception\Http\RedirectionException');
        throw new RedirectionException($url);
    }

    public function testRedirectionExceptionWithCustomHttpCode()
    {
        $url = 'http://www.example.com';
        $this->setExpectedException('Ardi\Exception\Http\RedirectionException');
        throw new RedirectionException($url, 302);
    }
}
