<?php

class Autoload
{
    public static function register(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Composer Autoload
        |--------------------------------------------------------------------------
        */

        require_once __DIR__ . '/../vendor/autoload.php';


        /*
        |--------------------------------------------------------------------------
        | Load Environment Variables
        |--------------------------------------------------------------------------
        */

        $dotenv = Dotenv\Dotenv::createImmutable(
            dirname(__DIR__)
        );

        $dotenv->safeLoad();


        /*
        |--------------------------------------------------------------------------
        | Load Helper Functions
        |--------------------------------------------------------------------------
        */

        require_once __DIR__ . '/../config/env.php';


        /*
        |--------------------------------------------------------------------------
        | Class Autoloader
        |--------------------------------------------------------------------------
        */

        spl_autoload_register(function ($class) {

            $directories = [

                __DIR__ . '/../config/',
                __DIR__ . '/../core/',
                __DIR__ . '/../models/',
                __DIR__ . '/../controller/',
                __DIR__ . '/../services/'

            ];


            foreach ($directories as $directory) {

                $file = $directory . $class . '.php';

                if (file_exists($file)) {

                    require_once $file;

                    return;
                }
            }

        });
    }
}