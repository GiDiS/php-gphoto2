<?php

error_reporting( E_ALL | E_STRICT );

$path = array(
    __DIR__.'/../library',
    get_include_path(),
);
set_include_path(implode(PATH_SEPARATOR, $path));

include 'Gphoto2/Gphoto2.php';

