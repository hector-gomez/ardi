<?php

namespace Ardi;


/**
 * Represents the current request and can route it to the right controller.
 *
 * @TODO Turn all error messages into exceptions, where the headers are issued and execution ends
 * @package Ardi
 */
class Request
{
    private static $instance = null;

    //Expected parameters in the request. They are injected by Apache's mod_rewrite
    private $expectedRouteParams = array('lang', 'view');

    private $routeParams = array();

    private $queryParams = array();

    //TODO Leave langDir empty and call other methods without it if not set, to respect other classes' defaults
    private $langDir = 'lang';
    private $layoutsDir;
    private $viewsDir;

    /**
     * Obtain an instance of this class that is shared by all classes.
     *
     * @return Request Shared instance
     */
    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Initialises the instance reading the request parameters.
     */
    private function __construct()
    {
        // Reads the parameters received in the request
        foreach ($this->expectedRouteParams as $p) {
            $this->routeParams[$p] = Utils::sanitizeString($_REQUEST[$p]);
        }

        // The rest of the parameters in a GET request are considered part of the query string
        foreach ($_GET as $key=>$value) {
            if (!in_array($key, $this->expectedRouteParams)) {
                $this->queryParams[$key] = Utils::sanitizeString($value);
            }
        }
    }

    /**
     * Creates the requested view and populates it with its language strings
     *
     * @param string $viewName Name of the view to load (may change from one language to another)
     * @param string $lang Language to use
     * @return View The populated view object
     */
    private function bootstrapView($viewName, $lang)
    {
        if (empty($viewName)) {
            $viewName = 'root';
        }
        $routeConfig = ConfigReader::getReader('routes');
        $route = $routeConfig->get($lang . '.' . $viewName);
        if (empty($route)) {
            ResponseHelper::issueNotFoundError("Requested view ($viewName) does not exist for this language ($lang)");
            return false;
        }
        $i18n = new Translator($lang, $route, $this->langDir);
        //TODO Read what layout to use from a configuration file ("default" if not set)
        $view = new View($route);
        $view->setTranslator($i18n);
        if (isset($this->layoutsDir)) {
            $view->setLayoutsDir($this->layoutsDir);
        }
        if (isset($this->viewsDir)) {
            $view->setViewsDir($this->viewsDir);
        }

        $view->description = $i18n->get('meta_description');
        $view->keywords = $i18n->get('meta_keywords');
        $view->lang = $lang;
        $windowTitle = $i18n->get('window_title');
        $view->title = empty($windowTitle) ? $i18n->get('site_name') : $windowTitle . ' - ' . $i18n->get('site_name');

        return $view;
    }

    /**
     * Process the request and produce a response.
     *
     * @param bool $print (optional) Whether to print the response, set to false if you only want to capture it
     * @return string|bool The response that will be outputted (HTTP code 200) or false if the request did not succeed
     */
    public function dispatch($print = true)
    {
        if ($this->isFormSubmission()) {
            $this->dispatchContactForm();
            return false;
        }

        $lang = $this->processLanguage();
        if ($lang === false) {
            return false;
        }

        $view = $this->bootstrapView($this->getRouteParam('view'), $lang);
        return $view ? $view->render($print) : false;

    }

    /**
     * Process a contact form submission and redirects the user so that the page can be reloaded safely.
     */
    public function dispatchContactForm()
    {
        $formProcessor = new ContactFormProcessor($_POST);
        $success = $formProcessor->submit();

        // Redirects the user to where it came from, using a GET request and including a status parameter
        $redirectUrl = Utils::sanitizeString($_SERVER['REQUEST_URI']);
        $redirectUrl .= empty($this->queryParams) ? '?' : '&';
        $redirectUrl .= 'status=' . ($success ? 'sent' : 'error');
        ResponseHelper::redirectTo($redirectUrl, 303);
    }

    /**
     * Retrieve the value of a parameter passed in the query string.
     *
     * @param string $key
     * @return string
     */
    public function getQueryParam($key)
    {
        return $this->queryParams[$key];
    }

    /**
     * Retrieve the value of a parameter that rules how this request will be dispatched.
     *
     * @param string $key
     * @return string
     */
    public function getRouteParam($key)
    {
        return $this->routeParams[$key];
    }

    /**
     * Evaluates the current request to identify if a form is being submitted.
     *
     * @return bool Whether the current request is a form submission
     */
    public function isFormSubmission()
    {
        $contentType = $_SERVER['CONTENT_TYPE'];
        $formContentTypes = array('application/x-www-form-urlencoded', 'multipart/form-data', 'text/plain');
        return !empty($_POST) && in_array($contentType, $formContentTypes);
    }

    /**
     * Reads the selected language from the request and makes sure it's supported
     *
     * @return string|bool The selected language or false if the request should not proceed
     */
    private function processLanguage()
    {
        $appConfig = ConfigReader::getReader('app');
        $lang = $this->getRouteParam('lang');

        // Redirect to the default language if not set
        if (empty($lang)) {
            ResponseHelper::redirectTo($appConfig->get('languages.default'), 303);
            return false;
        }

        // Make sure the selected language is supported
        if (!in_array($lang, $appConfig->get('languages.supported'))) {
            ResponseHelper::issueBadRequestError('Selected language not supported');
            return false;
        }

        return $lang;
    }

    /**
     * Changes the path where translation files are found
     *
     * @param string $langDir
     */
    public function setLangDir($langDir)
    {
        $this->langDir = $langDir;
    }

    /**
     * Changes the path where layout files are found
     *
     * @param string $layoutsDir
     */
    public function setLayoutsDir($layoutsDir)
    {
        $this->layoutsDir = $layoutsDir;
    }

    /**
     * Changes the path where view files are found
     *
     * @param string $viewsDir
     */
    public function setViewsDir($viewsDir)
    {
        $this->viewsDir = $viewsDir;
    }
}
