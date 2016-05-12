<?php
namespace UnfuddleReport\Controllers;

use \Psr\Http\Message\ServerRequestInterface as request;

class Auth
{

    const SESSION_AUTH_KEY = 'user_auth';
    const SESSION_PASS_KEY = 'user_pass';
    const SESSION_URL_KEY = 'user_url';

    const COOKIE_NAME = 'last_used_url';

    private static $query_params;
    private static $login_required = array(
        'username',
        'password',
        'unfuddle_url'
    );

    /**
     * Validate the session key
     * @return bool|void
     */
    public static function validate()
    {

        // Check if the session variable is not empty
        if (!empty($_SESSION[static::SESSION_AUTH_KEY])) {
            return static::verifyUser();
        }
        
        // Return false
        return false;
    }

    /**
     * Login as a user through the api call
     *
     * @param request $request
     * @param $arguments
     * @return bool
     */
    public static function login(request $request, &$arguments)
    {

        // Set the authenticated parameter
        $arguments['authenticated'] = false;

        // If the request is a post, then we want to perform a login attempt
        if ($request->isPost()) {

            // Save the query params
            self::$query_params = $request->getParsedBody();

            // All required fields are set.
            if (self::check_post(self::$query_params)) {

                // Set the session items
                $_SESSION[self::SESSION_AUTH_KEY] = self::$query_params['username'];
                $_SESSION[self::SESSION_PASS_KEY] = self::$query_params['password'];
                $_SESSION[self::SESSION_URL_KEY] = self::$query_params['unfuddle_url'];

                // Verify the user with the recently set session variables.
                if (self::verifyUser()) {

                    // Successfully logged in
                    $arguments['success'] = ['message' => 'You have successfully logged in, you will be redirected automatically.'];

                    // Set the authenticated parameter
                    $arguments['authenticated'] = true;

                    // Set the cookie for the user to be authenticated
                    setcookie(self::COOKIE_NAME, self::$query_params['unfuddle_url'], time()+(60 * 60 * 24 * 30));


                } else {
                    // Unset the session variables
                    $_SESSION[self::SESSION_AUTH_KEY] = null;
                    $_SESSION[self::SESSION_PASS_KEY] = null;
                    $_SESSION[self::SESSION_URL_KEY] = null;

                    // Set the error
                    $arguments['errors'][] = ['message' => 'The provided login credentials were incorrect, please try again.'];
                }

            } else {
                $arguments['errors'][] = ['message' => 'You failed to completely fill out the form.'];
            }

        }

        // Return true or false
        return !empty($arguments['authenticated']);
    }

    /**
     * Verify the user using session
     *
     * @return boolean
     */
    private static function verifyUser()
    {
        // Set the Base Url, Username and Password
        Api::setBaseUrl($_SESSION[self::SESSION_URL_KEY]);
        Api::setUsername($_SESSION[self::SESSION_AUTH_KEY]);
        Api::setPassword($_SESSION[self::SESSION_PASS_KEY]);

        // With the required parameters set, return the project list
        return !empty(Projects::getProjectList());
    }

    /**
     * Make sure everything required for login is set.
     *
     * @param $query_params
     * @return bool
     */
    private static function check_post($query_params)
    {

        // Loop through required and check that everything is set.
        foreach (self::$login_required as $req) {
            if (empty($query_params[$req])) {
                return false;
            }
        }

        return true;
    }

}