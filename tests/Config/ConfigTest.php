<?php

use Luracast\Config\Config;

class ConfigTest extends PHPUnit_Framework_TestCase
{
    public function testLoadConfigFile()
    {
        $path = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'fixtures';
        $config = new Config($path);
        $this->assertArrayHasKey('property', $config['file']);
    }

    public function testLoadConfigFileProperty()
    {
        $path = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'fixtures';
        $config = new Config($path);
        $this->assertEquals('value', $config['file.property']);
    }

    public function testLoadConfigData()
    {
        $path = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'fixtures';
        Config::init($path);
        $this->assertArrayHasKey('property', Config::get('file'));
        $this->assertEquals('value', Config::get('file.property'));
    }
}
 