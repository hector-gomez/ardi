<?php

namespace Ardi;


/**
 * Collection of helpers that are used throughout the framework.
 *
 * @package Ardi
 */
class Utils
{
    /**
     * Use this method to prevent injection and XSS attacks.
     *
     * @param string $value Value to sanitize
     * @return string Safely cleaned up value
     */
    public static function sanitizeString($value)
    {
        $value = trim($value);
        $value = stripslashes($value);
        $value = strip_tags($value);
        $value = htmlspecialchars($value, ENT_QUOTES);
        return $value;
    }
}
