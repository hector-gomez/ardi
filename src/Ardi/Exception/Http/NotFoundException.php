<?php

namespace Ardi\Exception\Http;

use Ardi\ResponseHelper;
use Exception;


class NotFoundException extends Exception
{
    public function __construct($message = null, $code = 0, Exception $previous = null)
    {
        if (is_null($message)) {
            ResponseHelper::issueNotFoundError();
        } else {
            ResponseHelper::issueNotFoundError($message);
        }
        parent::__construct($message, $code, $previous);
    }
}
