<?php

namespace Ardi\Exception\Http;

use Ardi\ResponseHelper;
use Exception;


class BadRequestException extends Exception
{
    public function __construct($message = null, $code = 0, Exception $previous = null)
    {
        if (is_null($message)) {
            ResponseHelper::issueBadRequestError();
        } else {
            ResponseHelper::issueBadRequestError($message);
        }
        parent::__construct($message, $code, $previous);
    }
}
