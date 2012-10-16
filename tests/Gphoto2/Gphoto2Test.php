<?php

namespace Gphoto2Test;



class Gphoto2Test extends \PHPUnit_Framework_TestCase
{
    protected $gphoto;
    
    
    public function setUp()
    {
        $this->gphoto = new \Gphoto2\Gphoto2;
    }
    
    public function testGetExecutable()
    {
        $this->assertNotEmpty($this->gphoto->getExecutable());
    }

    public function testAutoDetect()
    {
        $this->assertNotEmpty($this->gphoto->autoDetect());
    }
    
    public function testListCameras()
    {
        $this->assertNotEmpty($this->gphoto->listCameras());
    }
    
    public function testListPorts()
    {
        $this->assertNotEmpty($this->gphoto->listPorts());
    }
    
    public function testListConfig()
    {
        $this->assertNotEmpty($this->gphoto->listConfig());
    }

    public function testListAllConfig()
    {
        $this->assertNotEmpty($this->gphoto->listAllConfig());
    }
    
    public function testSetConfig()
    {
        foreach ($this->gphoto->listAllConfig() as $key => $config)
            $this->assertTrue($this->gphoto->setConfig($key, $config['Current']));
        $this->assertFalse($this->gphoto->setConfig('/234/', 'test'));
    }

    public function testSetConfigValue()
    {
        foreach ($this->gphoto->listAllConfig() as $key => $config)
            if ($config['Type'] !== 'MENU')
                $this->assertTrue($this->gphoto->setConfigValue($key, $config['Current']));
        $this->assertFalse($this->gphoto->setConfigValue('/234/', 'test'));
    }
    
    public function testSetConfigIndex()
    {
        foreach ($this->gphoto->listAllConfig() as $key => $config)
            if ($config['Type'] === 'MENU')
                $this->assertTrue($this->gphoto->setConfigIndex($key, $config['Current']));
        $this->assertFalse($this->gphoto->setConfigIndex('/234/', 'test'));
    }
    
    public function testCaptureImageAndDownload()
    {
        $result = $this->gphoto->captureImageAndDownload();
        $this->assertNotEmpty($result);
        
        file_put_contents('/tmp/1.jpg', $result);
    }
}

