<?php

namespace Lcobucci\ActionMapper2\Config;

use stdClass;

interface RouteLoader
{
    /**
     * @param $file
     * @return stdClass
     */
    public function load($file);
}
