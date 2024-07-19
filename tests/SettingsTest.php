<?php

use Cyclonecode\NewsDataIO\Plugin\Settings\Settings;
use PHPUnit\Framework\TestCase;

class SettingsTest extends TestCase
{
    const SETTINGS_NAME = 'tmpSetting';

    /** @var Settings */
    private $settings;

    /**
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->settings = new Settings(self::SETTINGS_NAME);
        $this->settings->delete();
    }

    public function testToOptionsArray()
    {
        $this->assertIsArray($this->settings->toOptionsArray());
    }

    public function testRename()
    {
        $this->settings->set('foo', 'bar');
        $this->assertArrayHasKey('foo', $this->settings->toOptionsArray());
        $this->settings->rename('foo', 'bar');
        $this->assertArrayHasKey('bar', $this->settings->toOptionsArray());
        $this->assertEquals($this->settings->get('bar'), 'bar');
        $this->assertArrayNotHasKey('foo', $this->settings->toOptionsArray());
    }

    public function testRemove()
    {
        $this->settings->set('foo', 'bar');
        $this->settings->set('bar', 'foo');
        $this->assertArrayHasKey('foo', $this->settings->toOptionsArray());
        $this->assertArrayHasKey('foo', $this->settings->toOptionsArray());
        $this->settings->remove('bar');
        $this->assertArrayNotHasKey('bar', $this->settings->toOptionsArray());
        $this->assertArrayHasKey('foo', $this->settings->toOptionsArray());
    }

    public function testSave()
    {
        $this->assertEmpty($this->settings->toOptionsArray());
        $this->settings->set('foo', 'bar');
        $this->settings->save();
        $this->settings->load();
        $this->assertNotEmpty($this->settings->toOptionsArray());
        $this->assertArrayHasKey('foo', $this->settings->toOptionsArray());
        $this->assertEquals('bar', $this->settings->get('foo'));
    }

    public function testDelete()
    {
        $this->assertEmpty($this->settings->toOptionsArray());
        $this->settings->set('foo', 'bar');
        $this->settings->save();
        $this->settings->load();
        $this->assertNotEmpty($this->settings->toOptionsArray());
        $this->settings->delete();
        $this->assertEmpty($this->settings->toOptionsArray());
        $this->settings->load();
        $this->assertEmpty($this->settings->toOptionsArray());
    }

    public function testCount()
    {
        $this->settings->set('foo', 'bar');
        $this->settings->set('bar', 'foo');
        $this->assertIsArray($this->settings->toOptionsArray());
        $this->assertNotEmpty($this->settings->toOptionsArray());
        $this->assertCount(2, $this->settings->toOptionsArray());
        $this->assertEquals(2, $this->settings->count());
    }

    public function testGetFromArray()
    {
        $this->settings->set('foo', 'bar');
        $this->settings->set('items', [
            'bar',
            'baz',
        ]);
        $this->assertEquals('bar', $this->settings->getFromArray('items', 0));
        $this->assertEquals('baz', $this->settings->getFromArray('items', 1));
    }

    public function testSetFromArray()
    {
        $this->settings->set('x', 'y');
        $this->settings->setFromArray([
            'foo' => 'bar',
            'bar' => 'foo',
        ]);
        $this->assertCount(3, $this->settings->toOptionsArray());
        $this->assertEquals('bar', $this->settings->get('foo'));
        $this->assertEquals('foo', $this->settings->get('bar'));
    }

    public function testHas()
    {
        $this->settings->set('foo', 'bar');
        $this->assertTrue($this->settings->has('foo'));
        $this->assertFalse($this->settings->has('bar'));
    }

    public function testHasKey()
    {
        $this->settings->set('foo', 'bar');
        $this->assertTrue($this->settings->hasKey('foo'));
        $this->assertFalse($this->settings->hasKey('bar'));
    }

    public function testClean()
    {
        $this->settings->setFromArray([
            'foo' => 'bar',
            'bar' => 'foo',
            'key' => 'value',
        ]);
        $this->assertIsArray($this->settings->toOptionsArray());
        $this->assertArrayHasKey('foo', $this->settings->toOptionsArray());
        $this->assertArrayHasKey('bar', $this->settings->toOptionsArray());
        $this->assertArrayHasKey('key', $this->settings->toOptionsArray());
        $this->settings->clean(['foo']);
        $this->assertArrayHasKey('foo', $this->settings->toOptionsArray());
        $this->assertArrayNotHasKey('bar', $this->settings->toOptionsArray());
        $this->assertArrayNotHasKey('key', $this->settings->toOptionsArray());
    }

    public function testToJson()
    {
        $data = [
            'foo' => 'bar',
            'bar' => 'foo',
            'key' => 'value',
        ];
        $this->settings->setFromArray($data);
        $jsonData = $this->settings->toJSON();
        $this->assertIsString($jsonData);
        $this->assertNotNull($jsonData);
        $decoded = \json_decode($jsonData, JSON_OBJECT_AS_ARRAY);
        $this->assertIsArray($decoded);
        $this->assertEquals($data, $decoded);
    }

    public function test_saveToFile_Raw()
    {
        $filename = sys_get_temp_dir() . '/test.json';
        $this->settings->saveToFile($filename, 'raw');
        $this->assertTrue(true);
    }

    public function test_saveToFile_Json()
    {
        $filename = sys_get_temp_dir() . '/test.json';
        $this->settings->saveToFile($filename, 'json');
        $this->assertTrue(true);
    }

    public function test_toYaml()
    {
        $this->assertNotNull($this->settings->toYaml());
    }

    public function test_saveToFile_Yaml()
    {
        $filename = sys_get_temp_dir() . '/test.json';
        $this->settings->saveToFile($filename, 'yaml');
        $this->assertTrue(true);
    }

    public function testGetOptionName()
    {
        $this->assertIsString($this->settings->getOptionName());
        $this->assertEquals(self::SETTINGS_NAME, $this->settings->getOptionName());
    }

    public function testGetVersion()
    {
        $this->assertIsString($this->settings->getVersion());
        $this->assertNotEmpty($this->settings->getVersion());
    }

    public function testUnset()
    {
        $this->settings->set('foo', 'bar');
        $this->settings->set('bar', 'baz');
        $this->settings->__unset('foo');
        $this->assertEquals(1, count($this->settings));
    }

    public function testOffsetExists()
    {
        $this->settings->set('foo', 'bar');
        $this->assertTrue(isset($this->settings['foo']));
    }

    public function testOffsetGet()
    {
        $this->settings->set('foo', 'bar');
        $this->assertEquals('bar', $this->settings['foo']);
    }

    public function testOffsetSet()
    {
        $this->settings['foo'] = 'bar';
        $this->assertEquals('bar', $this->settings->get('foo'));
    }

    public function testOffsetUnset()
    {
        $this->settings->set('foo', 'bar');
        unset($this->settings['foo']);
        $this->assertNull($this->settings->get('foo'));
    }

    public function testGetIterator()
    {
        $this->settings->set('foo', 'bar');
        $this->settings->set('bar', 'baz');
        foreach ($this->settings as $key => $setting) {
            $this->settings->remove($key);
        }
        $this->assertEmpty($this->settings->toOptionsArray());
    }

    public function testAdd()
    {
        $this->settings->set('foo', 'bar');
        $this->settings->add('bar', 'baz');
        $this->assertEquals(2, count($this->settings));
    }
}
