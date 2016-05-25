<?php

use \Slim\Http\Response;
use \Slim\Http\Request;
use \UnfuddleReport\Controllers\Auth;

/**
 * Middleware for login authentication
 * 
 * @var Request $request
 * @var Response $response
 * @var callable $next
 * @return Response
 */
$authentication_middleware = function ($request, $response, $next) {

    // Set logged in to false by default
    $logged_in = false;

    // Validate the user
    $user = Auth::validate();

    // The user was returned
    if (!empty($user)) {
        $logged_in = true;

        // Save the user in the DIC
        $this['auth_user'] = $user;
    }

    // Redirect if not logged in and on a page other than /
    if ($logged_in === false && $request->getUri()->getPath() !== '/') {
        header('Location: /');
        exit;
    }

    // If we logged in correctly then came back then we can automatically redirect to the options page.
    if ($request->getUri()->getPath() === '/' && $logged_in) {
        header('Location: /options');
        exit;
    }

    // Call next in middleware
    $response = $next($request, $response);

    // Return response
    return $response;

};