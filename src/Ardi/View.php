<?php

namespace Ardi;


/**
 * Represents and manages a view
 *
 * @package Ardi
 */
class View
{
    private $layoutsDir = 'layouts';
    private $layoutFile;

    private $viewsDir = 'views';
    private $viewFile;
    private $viewName;

    // Main view variables
    public $body;
    public $charset = 'utf-8';
    public $description;
    public $keywords;
    public $lang;
    public $title;

    // Other special view variables
    private $i18n;
    private $scriptFilesBody = array();
    private $scriptFilesHead = array();
    private $stylesheetFiles = array();

    /**
     * @param string $viewName Global name of the view (usually in English)
     * @param string $layoutName Name of the layout in which to render this view
     */
    public function __construct($viewName, $layoutName = 'default')
    {
        $this->layoutFile = $layoutName;
        $this->viewFile = $this->viewName = $viewName;
    }

    /**
     * Adds a script to the current view. By default considers the path to be relative to the application's static
     * folder and to be inserted inside the <head> tag.
     *
     * @param string $path Relative path to the JS file or absolute URL if the absolute parameter is set to true
     * @param bool $toBodyTag (optional) If true will add it to the body tag, otherwise to the head (by default)
     * @param bool $isAbsolute (optional) If true the provided URL is considered absolute and left untouched
     */
    public function addScript($path, $toBodyTag = false, $isAbsolute = false)
    {
        if ($toBodyTag) {
            $array =& $this->scriptFilesBody;
        } else {
            $array =& $this->scriptFilesHead;
        }
        $array[] = $isAbsolute ? $path : $this->getStaticFileUrl($path);
    }

    /**
     * Adds a stylesheet to the current view
     *
     * @param string $path Relative path to the CSS file or absolute URL if the absolute parameter is set to true
     * @param bool $isAbsolute (optional) If true the provided URL is considered absolute and left untouched
     */
    public function addStylesheet($path, $isAbsolute = false)
    {
        $this->stylesheetFiles[] = $isAbsolute ? $path : $this->getStaticFileUrl($path);
    }

    /**
     * Creates the necessary markup to load JavaScript files
     *
     * @param array $array The files array to use
     * @return string The generated HTML markup
     */
    private function generateScriptsMarkup($array)
    {
        $markup = '';
        for ($i = 0; $i < count($array); $i++) {
            $markup .= "<script src='$array[$i]'></script>";
        }
        return $markup;
    }

    /**
     * Returns the HTML necessary to load the JavaScript files that go inside the <body> tag.
     *
     * @return string HTML markup to be inserted inside the <body> tag
     */
    public function getBodyScriptsMarkup()
    {
        return $this->generateScriptsMarkup($this->scriptFilesBody);
    }

    /**
     * Returns the HTML necessary to load the JavaScript files that go inside the <head> tag.
     *
     * @return string HTML markup to be inserted inside the <head> tag
     */
    public function getHeadScriptsMarkup()
    {
        return $this->generateScriptsMarkup($this->scriptFilesHead);
    }

    /**
     * Builds the path to the layout file.
     *
     * @return string
     */
    public function getLayoutPath()
    {
        return $this->layoutsDir . DIRECTORY_SEPARATOR . $this->layoutFile . '.phtml';
    }

    /**
     * Creates an absolute URL to a static asset (like images, CSS or JS files).
     *
     * @param string $path Relative path to the file (do not include 'static')
     * @return string The absolute URL to the file
     */
    public function getStaticFileUrl($path)
    {
        return UrlHelper::buildBaseUrl().'/static/'.$path;
    }

    /**
     * Generates and returns the HTML necessary to load the CSS files.
     *
     * @return string HTML markup to be inserted inside the <head> tag
     */
    public function getStylesheetsMarkup()
    {
        $markup = '';
        for ($i = 0; $i < count($this->stylesheetFiles); $i++) {
            $markup .= "<link rel='stylesheet' type='text/css' href='{$this->stylesheetFiles[$i]}'>";
        }
        return $markup;
    }

    /**
     * Builds the path to the view file.
     *
     * @return string
     */
    public function getViewPath()
    {
        return $this->viewsDir . DIRECTORY_SEPARATOR . $this->viewFile . '.phtml';
    }

    /**
     * Obtains the url to a view in a specific language.
     *
     * @param string $viewName Global name of the view (usually in English)
     * @param string $lang (optional) Language, if not specified the current language will be used
     * @param string $queryString (optional) Query string to append to the URL
     * @return string|null The absolute URL to the view or null if not found
     */
    public function getViewUrl($viewName, $lang = '', $queryString = '')
    {
        $lang = ($lang === '') ? $this->lang : $lang;
        return UrlHelper::buildViewUrl($viewName, $lang, $queryString);
    }

    /**
     * Evaluates if this view can potentially be translated.
     *
     * @return bool Whether if this view is using a translator
     */
    public function isTranslated()
    {
        return isset($this->i18n);
    }

    /**
     * Computes the markup, returns it and prints it (by default).
     *
     * @param bool $print Whether to print the view in the response
     * @return string The markup for the view
     */
    public function render($print = true)
    {
        // Process the view
        ob_start();
        /** @noinspection PhpIncludeInspection */
        include $this->getViewPath();
        $this->body = ob_get_clean();

        // Process the layout, will use the view as $body
        ob_start();
        /** @noinspection PhpIncludeInspection */
        include $this->getLayoutPath();
        $markup = ob_get_clean();

        if ($print) {
            echo $markup;
        }

        return $markup;
    }

    /**
     * Changes the path where layouts are located.
     *
     * @param string $layoutsDir Path to the folder, relative to the repository root
     */
    public function setLayoutsDir($layoutsDir)
    {
        $this->layoutsDir = $layoutsDir;
    }

    /**
     * Assigns a translator for the view. That defines the language strings that will be used.
     *
     * @param Translator $translator Translator instance to use in this view
     */
    public function setTranslator(Translator $translator)
    {
        $this->i18n = $translator;
    }

    /**
     * Changes the path where views are located.
     *
     * @param string $viewsDir Path to the folder, relative to the repository root
     */
    public function setViewsDir($viewsDir)
    {
        $this->viewsDir = $viewsDir;
    }
}
