<?php

namespace UnfuddleReport\Controllers;

use UnfuddleReport\Models\User;

class Users extends Api
{

    // People Endpoint
    const PEOPLE_ENDPOINT = '/api/v1/people.json';

    protected static $save_key = 'users_list';

    /**
     * Retrieve the People list from api
     *
     * @return array
     */
    public static function getUserList()
    {

        // Initiate a project list
        $user_list = array();

        try {

            // If there is a saved project list in session, use that
            if (static::has_list()) {
                $people = static::get_list();
            } else {

                // Get the Projects from the api
                $people = static::get(static::PEOPLE_ENDPOINT);

                // Save the list of users in session
                static::save_list($people);

            }

            // Iterate through the returned People
            if (!empty($people)) {

                foreach ($people as $person) {
                    $user_list[] = new User($person);
                }
            }

        } catch (\Exception $e) {
            echo $e->getMessage() . PHP_EOL;
        }

        // Return the array
        return $user_list;
    }

    /**
     * Get a specific user from the api
     * 
     * @param $user_id
     * @return bool|mixed
     */
    public static function getUser($user_id)
    {
        // Get the list, which should be cached locally
        $list = static::getUserList();

        // Loop through the users and return on that matches the id
        foreach ($list as $user) {
            if ($user->id == $user_id) {
                return $user;
            }
        }
        
        return false;
    }

}