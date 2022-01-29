<?php

use Luracast\Config\Config;
use PHPUnit\Framework\TestCase;

class ConfigTest extends TestCase
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

    public function testSetNestedProperty()
    {
        $path = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'fixtures';
        Config::init($path);
        Config::set('file.nested.property', 'value');
        $this->assertEquals('value', Config::get('file.nested.property'));
    }

    public function testSetNestedPropertyWithArray()
    {
        $path = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'fixtures';
        Config::init($path);
        Config::set('file.nested.property', ['value']);
        $this->assertEquals(['value'], Config::get('file.nested.property'));
    }

    public function testSetNestedPropertyWithArrayAndString()
    {
        $path = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'fixtures';
        Config::init($path);
        Config::set('file.nested.property', ['value', 'value2']);
        $this->assertEquals(['value', 'value2'], Config::get('file.nested.property'));
    }

}
