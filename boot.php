<?php
set_include_path(get_include_path() . PATH_SEPARATOR . __DIR__ . '/src');

require 'Lcobucci/ActionMapper2/ClassLoader/SplClassLoader.php';

use Lcobucci\ActionMapper2\ClassLoader\SplClassLoader;

$loader = new SplClassLoader();
$loader->register();