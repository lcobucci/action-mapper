<?php
use Doctrine\Common\Annotations\AnnotationRegistry;

$autoloader = require __DIR__ . '/vendor/autoload.php';
AnnotationRegistry::registerLoader(array($autoloader, 'loadClass'));
