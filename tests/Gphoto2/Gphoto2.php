<?php

require '../../library/Gphoto2/Gphoto2.php';

$gphoto = new \Gphoto2\Gphoto2;

var_dump($gphoto->getExecutable());
#var_dump($gphoto->listCameras());
#var_dump($gphoto->listPorts());
#var_dump($gphoto->autoDetect());
#var_dump($gphoto->listConfig());
var_dump($gphoto->listAllConfig());