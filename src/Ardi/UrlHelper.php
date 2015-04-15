<?php

namespace Ardi;


/**
 * Provides mechanisms to generate URLs to different resources
 *
 * @package Ardi
 */
class UrlHelper
{
    private static $cachedBaseUrl;
    private static $cachedViewUrls = array();

    /**
     * Generates the URL to the website relative to the server.
     *
     * @return string Base URL of the website, without trailing slash
     */
    public static function buildBaseUrl()
    {
        if (!isset(self::$cachedBaseUrl)) {
            // All requests go to public/index.php
            $path = explode('/public/index.php', filter_var($_SERVER['PHP_SELF'], FILTER_SANITIZE_STRING));
            self::$cachedBaseUrl = $path[0];
        }
        return self::$cachedBaseUrl;
    }

    /**
     * Generates the url to a view in a specific language.
     *
     * @param string $viewName Global name of the view (usually in English)
     * @param string $lang Language the view will be showed in
     * @param string $queryString (optional) Query string to append to the URL
     * @return string|null The URL to the view or null if not found
     */
    public static function buildViewUrl($viewName, $lang, $queryString = '')
    {
        $cacheKey = $viewName.'-'.$lang;
        if (!isset(self::$cachedViewUrls[$cacheKey])) {
            $routeConfig = ConfigReader::getReader('routes');
            $routes = $routeConfig->get($lang);
            $translatedName = array_search($viewName, $routes);
            if (!$translatedName) {
                return null;
            }
            $builtUrl = self::buildBaseUrl() . "/$lang";
            $builtUrl .= ($translatedName === 'root') ? '' : '/'.$translatedName;
            self::$cachedViewUrls[$cacheKey] = $builtUrl;
        }
        return self::$cachedViewUrls[$cacheKey].$queryString;
    }

    /**
     * Removes the cached urls.
     */
    public static function clearCache()
    {
        self::$cachedBaseUrl = null;
        self::$cachedViewUrls = array();
    }
}
