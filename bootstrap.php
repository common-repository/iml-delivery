<?php

// If this file is called directly, abort.
// if (!defined( 'WPINC')) {
//     die;
// }

$iml_delivery_spl_autoloader = true;

function iml_autoloader($class)
{
        $path = explode('\\', $class);

        if ($path[0] != 'Iml') {
            return;
        }

        $path[0] = 'includes';
        $filePath = dirname(__FILE__) . '/' . implode('/', $path) . '.php';
		//echo $filePath.PHP_EOL;
        if (file_exists($filePath)) {
            require_once($filePath);
        }
}

spl_autoload_register('iml_autoloader', true, true);