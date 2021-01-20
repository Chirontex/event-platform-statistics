<?php
/**
 * Event Platform Statistics
 */
spl_autoload_register(function($classname) {

    if (strpos($classname, 'EPStatistics') !== false) {

        $path = __DIR__.'/kernel/';

        $file = explode('\\', $classname);

        if (isset($file[count($file) - 2])) {

            switch ($file[count($file) - 2]) {

                case 'Handlers':
                    $path .= 'handlers/';
                    break;

                case 'Exceptions':
                    $path .= 'exceptions/';
                    break;

                case 'Traits':
                    $path .= 'traits/';
                    break;

            }

        }

        $file = $file[count($file) - 1].'.php';

        if (file_exists($path.$file)) require_once $path.$file;

    }

});
