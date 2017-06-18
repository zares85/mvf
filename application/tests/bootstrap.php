<?php

require_once dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

// This is to avoid "No direct script access allowed".
defined('BASEPATH') || define('BASEPATH', dirname(__DIR__) . DIRECTORY_SEPARATOR . 'system');
defined('APPPATH') || define('APPPATH', dirname(__DIR__) . DIRECTORY_SEPARATOR);

// Autoload libraries, entities and interfaces.
spl_autoload_register(function ($class) {
    foreach (['libraries', 'entities', 'interfaces'] as $dir) {
        $file = APPPATH . $dir . DIRECTORY_SEPARATOR . $class . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});