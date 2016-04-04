<?php

namespace UnfuddleReport\Controllers;

use Psr\Http\Message\ServerRequestInterface;

class Options
{

    const SESSION_KEY = 'report_options';
    
    private $projects;
    private $users;

    /**
     * Options constructor.
     * @param ServerRequestInterface $request
     */
    public function __construct($request)
    {

        // Get the parsed body
        $parsed_body = $request->getParsedBody();

        if (!empty($parsed_body)) {
            
            // Set the projects
            if (!empty($parsed_body['projects'])) {
                $this->projects = $parsed_body['projects'];
            }
            // Set the users
            if (!empty($parsed_body['users'])) {
                $this->users = $parsed_body['users'];
            }
            
        }
        
    }

    /**
     * Make sure both fields are set.
     * @return bool
     */
    public function validate()
    {
        return (!empty($this->projects) && !empty($this->users));
    }

    /**
     * Save the options in session
     */
    public function save()
    {
        // Check that it is valid
        if ($this->validate()) {
            $_SESSION[self::SESSION_KEY] = json_encode(array(
                'projects' => $this->projects,
                'users' => $this->users
            ));
            return true;
        }
        return false;
    }

    /**
     * Retrieve the options from session
     * 
     * @return bool
     */
    public static function get()
    {
        if (!empty($_SESSION[self::SESSION_KEY])) {
            return json_decode($_SESSION[self::SESSION_KEY], true);
        }
        return false;
    }

    /**
     * @return array
     */
    public function getUsers()
    {
        return $this->users;
    }

    /**
     * @return array
     */
    public function getProjects()
    {
        return $this->projects;
    }



}