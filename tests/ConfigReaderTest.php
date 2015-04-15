<?php


use Ardi\ConfigReader;

class ConfigReaderTest extends PHPUnit_Framework_TestCase {
    public function setUp()
    {
        ConfigReader::setConfigDir('tests/fixtures/config');
    }

    public function testDependencyInjection()
    {
        $configDir = 'path/to/configuration';
        ConfigReader::setConfigDir($configDir);
        $this->assertEquals($configDir, ConfigReader::getConfigDir(), 'Failed setting the configuration directory');
    }

    public function testLanguages()
    {
        $reader = ConfigReader::getReader('app');
        $this->assertEquals('en', $reader->get('languages.default'));
        $supported = $reader->get('languages.supported');
        $this->assertGreaterThan(0, count($supported), 'There must be at least one supported language');
    }
}
