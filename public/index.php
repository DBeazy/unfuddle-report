<?php

// Set the app_path to the root dir
define('APP_PATH', '../');

// Require composer auto loader
require_once APP_PATH . 'vendor/autoload.php';

// Set the session cookie lifetime to 15 days
$session_lifetime = 60 * 60 * 24 * 15;
session_set_cookie_params($session_lifetime);

// Require session on every request
session_start();

// Require the dependency injector
require_once APP_PATH . 'app/services.php';

// Instantiate the app
$app = new \Slim\App($di);

// Register middleware
require_once APP_PATH . 'app/middleware.php';

// Register routes
require_once APP_PATH . 'app/routes.php';


// Run the app
$app->run();
