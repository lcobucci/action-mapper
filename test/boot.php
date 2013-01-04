<?php
set_include_path(get_include_path() . PATH_SEPARATOR . __DIR__ . '/../src');

require 'Lcobucci/Common/ClassLoader/SplClassLoader.php';
require 'Lcobucci/Common/ClassLoader/AnnotationReadyClassLoader.php';

use Lcobucci\Common\ClassLoader\AnnotationReadyClassLoader;

$loader = new AnnotationReadyClassLoader();
$loader->register();
