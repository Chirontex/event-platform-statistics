<?php
/**
 * Event Platform Statistics
 */
spl_autoload_register(function($classname) {

    if (strpos($classname, 'EPStatistics') !== false) {

        $path = __DIR__.'/classes/';

        $file = explode('\\', $classname);
        $file = $file[count($file) - 1].'.php';

        if (file_exists($path.$file)) require_once $path.$file;

    }

});
