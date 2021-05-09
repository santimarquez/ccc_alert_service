<?php

spl_autoload_register(function ($class_name) {
    $class_root_path = 'app\\';
    $class_name .= '.php';
    //Base level classes
    if (is_file($class_root_path . $class_name)) {
        include $class_root_path . $class_name;
    } else {
        //Model, Notifications and sources classes
        foreach (glob($class_root_path . '*', GLOB_ONLYDIR) as $dir_1) {
            if (is_file($dir_1 . '\\' . $class_name)) {
                include $dir_1 . '\\' . $class_name;
            } else {
                //Libraries
                foreach (glob($dir_1 . '\*', GLOB_ONLYDIR) as $dir_2) {
                    if (is_file($dir_2 . '\\' . $class_name)) {
                        include $dir_2 . '\\' . $class_name;
                    }
                }
            }
        }
    }
});
