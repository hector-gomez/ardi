<?php

namespace Ardi\Exception\Http;

use Ardi\ResponseHelper;
use Exception;


class RedirectionException extends Exception
{
    public function __construct($url, $httpCode = null, $code = 0, Exception $previous = null)
    {
        if (is_null($httpCode)) {
            ResponseHelper::redirectTo($url);
        } else {
            ResponseHelper::redirectTo($url, $httpCode);
        }
        parent::__construct("Redirecting to $url", $code, $previous);
    }
}
