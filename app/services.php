<?php

use \Slim\Container;
use \Slim\Views\Twig;

/**
 * This is the Container class that allows us to be able to inject dependencies.
 */
$di = new Container();

/**
 * Configuration array
 * @param $c Container instance
 * @return array $config Config options from ini file
 */
$di['config'] = function ($c) {
    return parse_ini_file(APP_PATH . 'app/config.ini', true);
};

/**
 * View 
 * @param $c Container instance
 * @return Twig
 */
$di['view'] = function ($c) {
    // Instantiate a new Twig View
    $view = new Twig(APP_PATH . 'templates', array(
        // If debug mode is set in config.ini then don't cache the templates, otherwise use the template cache directory
        'cache' => ($c['config']['debug']['enabled'] == true ? false : APP_PATH . 'templates/template-cache')
    ));
    // Add the internals of slim to the Twig Extension
    $view->addExtension(new \Slim\Views\TwigExtension(
        $c['router'],
        $c['request']->getUri()
    ));
    
    return $view;
};
