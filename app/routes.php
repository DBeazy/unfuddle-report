<?php

use UnfuddleReport\Controllers\Auth;
use UnfuddleReport\Controllers\Options;
use UnfuddleReport\Controllers\Projects;
use UnfuddleReport\Controllers\Report;
use UnfuddleReport\Controllers\Users;

/**
 * Get Report from ajax call
 */
$app->post('/get-report', function ($request, $response) {
    
    // Get the report
    $report = Report::getReport($this, $request);

    return $this->view->render($response, 'inner-report.html', ['report' => $report]);

})->add($authentication_middleware);

/**
 * Report Page 
 */
$app->get('/report', function ($request, $response, $args) {

    // If we reach the route then the middleware has authenticated us.
    $args['authenticated'] = true;

    // Check for options
    $args['session']['options'] = !empty(Options::get());

    // Set this endpoints url
    $args['url'] = '/report';
    
    // Set the default time and format
    $args['default_time'] = $this->config['date']['time'];
    $args['default_format'] = $this->config['date']['format'];

    return $this->view->render($response, 'report.html', $args);

})->setName('report')->add($authentication_middleware);

/**
 * Report Options Page
 */
$app->map(['GET', 'POST'], '/options', function ($request, $response, $args) {

    // If this is post then save the selections
    if ($request->isPost()) {
        
        // Get a new options class and then save it
        $options = new Options($request);
        
        // Get the args
        $args['request']['users'] = $options->getUsers();
        $args['request']['projects'] = $options->getProjects();
        
        // Make sure both fields are set
        if ($options->validate()) {
            // Save the data in session
            $options->save();
            
            // Save the success message
            $args['success']['message'] = 'You have saved the report options, you can now view the report.';

            // We have options so set true for this
            $args['session']['options'] = true;

        } else {
            // Display an error
            $args['errors'][] = ['message' => 'You must select at least one Project and one Developer.'];
        }

    } else {
        // Get the options from session
        $options = Options::get();

        if (!empty($options)) {
            // Set the args
            $args['request']['users'] = $options['users'];
            $args['request']['projects'] = $options['projects'];

            // We have options so set true for this
            $args['session']['options'] = true;
        }

    }

    // If we reach the route then the middleware has authenticated us.
    $args['authenticated'] = true;
    
    // Set this endpoints url
    $args['url'] = '/options';
    
    // We need the project list within this endpoint
    $args['projects'] = Projects::getProjectList();

    // We need the User list within this endpoint
    $args['users'] = Users::getUserList();

    return $this->view->render($response, 'report-options.html', $args);

})->setName('options')->add($authentication_middleware);

/**
 * Logout Page
 */
$app->get('/logout', function ($request, $response) {
    
    // Unset the session variables
    $_SESSION = array();
    
    // If we are using session cookies then delete that as well
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    
    // Destroy the session now
    session_destroy();

    // And now redirect to homepage
    return $response->withRedirect('/?logout');

})->setName('logout')->add($authentication_middleware);

/**
 * Homepage/Login Page
 */
$app->map(['GET', 'POST'], '/', function ($request, $response, $args) {

    // Put this through the UserController
    $logged_in = Auth::login($request, $args);

    // If logout is set then display the message
    if ($request->getQueryParam('logout') !== null) {
        $args['logout'] = true;
    }

    // Set this endpoints url
    $args['url'] = '/';

    // If we have successfully logged in then set a refresh for 3 seconds.
    if ($logged_in) {
        $response = $response->withHeader('refresh', '3;url=' . $this->router->pathFor('options'));
    }
    
    // If the cookie is set for the remember_me then we can show it in login form
    if (!empty($_COOKIE[Auth::COOKIE_NAME])) {
        $args['remember_url'] = $_COOKIE[Auth::COOKIE_NAME];
    }

    return $this->view->render($response, 'index.html', $args);

})->setName('login')->add($authentication_middleware);
