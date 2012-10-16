<?php

require '../../../library/Zee/Gphoto2/Wrapper.php';

$wrapper = new \Zee\Gphoto2\Wrapper;

var_dump($wrapper->getExecutable());
#var_dump($wrapper->listCameras());
#var_dump($wrapper->listPorts());
#var_dump($wrapper->autoDetect());
#var_dump($wrapper->listConfig());
var_dump($wrapper->listAllConfig());