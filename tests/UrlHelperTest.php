<?php


use Ardi\UrlHelper;

class UrlHelperTest extends PHPUnit_Framework_TestCase {
    public function setUpClass()
    {
        ConfigReader::setConfigDir('tests/fixtures/config');
    }

    public function setUp()
    {
        UrlHelper::clearCache();
    }

    public function testBaseUrlForRoot()
    {
        $_SERVER['PHP_SELF'] = '/public/index.php';
        $this->assertEmpty(UrlHelper::buildBaseUrl(), 'Websites served directly from the server root have empty relative URL');
    }

    public function testBaseUrlForNestedSite()
    {
        $_SERVER['PHP_SELF'] = '/blog/public/index.php';
        $this->assertEquals('/blog', UrlHelper::buildBaseUrl(), 'Base URL must be where in the server the site is');
    }

    public function testViewUrl()
    {
        $_SERVER['PHP_SELF'] = '/public/index.php';
        $this->assertEquals('/en', UrlHelper::buildViewUrl('home', 'en'));
        $this->assertEquals('/es', UrlHelper::buildViewUrl('home', 'es'));
        $this->assertEquals('/en?page=1', UrlHelper::buildViewUrl('home', 'en', '?page=1'));
        $this->assertEquals('/en/contact', UrlHelper::buildViewUrl('contact', 'en'));
        $this->assertEquals('/es/contacto', UrlHelper::buildViewUrl('contact', 'es'));
        $this->assertNull(UrlHelper::buildViewUrl('non-existent-view', 'en'));
    }
}
