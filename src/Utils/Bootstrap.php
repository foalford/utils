<?php

namespace Foalford\Utils;

use Zend\Stdlib\ArrayUtils;
use Zend\ServiceManager\ServiceManager;

/**
 * Bootstrap a service locator
 * 
 * @return function (string $name, bool $build = false) {}
 */
function bootstrap($configFiles)
{
    $config = array_reduce(glob($configFiles, GLOB_BRACE), function($conts, $file) {
		return ArrayUtils::merge($conts, include $file);
	}, ['service_manager'=>[]]);
    $sm = new ServiceManager($config['service_manager']);
	unset($config['service_manager']);
    $sm->setService('config', $config);
    return function ($name, $args = false) use ($sm) {
        return $args===false ? $sm->get($name): $sm->build($name, $args) ;
    };
}
