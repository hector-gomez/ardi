<?php

namespace Ardi;


/**
 * Provides methods to help create responses, or give predefined responses (like standard HTTP codes)
 *
 * @package Ardi
 */
class ResponseHelper
{
    /**
     * Returns a HTTP 404 response and terminates execution
     *
     * @param string $message Message to be displayed
     */
    public static function issueBadRequestError($message = 'Bad Request')
    {
        header($message, true, 400);
        echo $message;
    }

    /**
     * Returns a HTTP 404 response and terminates execution
     *
     * @param string $message Message to be displayed
     */
    public static function issueNotFoundError($message = 'Not found')
    {
        header($message, true, 404);
        echo $message;
    }

    /**
     * Issues a redirection. Be careful to use the right HTTP code (303 is the default)
     *
     * @param string $location Where to take the user
     * @param int $code HTTP code. Examples: 301 (moved permanently), 302 (moved temporarily) or 303 (see other)
     * @throws \Exception If the provided code is not a HTTP redirection code
     */
    public static function redirectTo($location, $code = 303)
    {
        if ($code < 300 || $code >= 400) {
            throw new \Exception("HTTP code $code is not a valid redirection code");
        }
        header("Location: $location", true, $code);
    }
}
