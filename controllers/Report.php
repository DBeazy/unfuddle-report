<?php

namespace UnfuddleReport\Controllers;

use Psr\Http\Message\ServerRequestInterface;
use Slim\Container;

class Report
{

    private static $di;
    private static $start_time;
    private static $users;
    private static $projects;

    public static function getReport(Container $di, ServerRequestInterface $request)
    {

        // Save the dependency injector locally
        static::$di = $di;
        
        // Get the parsed Body
        $parameters = $request->getParsedBody();

        // Set the start time
        static::setStartTime($parameters);

        // Unset the parsed body now.
        $parameters = null;
        unset($parameters);

        // Get the options
        if (!static::loadOptions()) {
            return ['errors' => ['message' => 'The report options were not set properly.']];
        }

        // Get the activity reports
        $activity_reports = Activity::getActivityList([
            'start_date' => static::$start_time,
            'projects' => static::$projects,
            'users' => static::$users
        ]);

        // Instantiate the full report
        $full_report = array();

        // Loop through the users to split on each person
        foreach (static::$users as $user) {

            // Get this user's activities
            $user_activities = array_filter($activity_reports, function ($item) use ($user) {
                if ($item->belongs_to == $user) {
                    return true;
                }
                return false;
            });

            // Merge the activities in User_activities
            $merged_activities = [];

            // Loop through each item to put into merged activities
            foreach ($user_activities as $user_activity) {

                // Get the ticket ids with every iteration as it is a dynamically changing array
                $ticket_ids = array_map(function($e) {
                    return is_object($e) ? $e->ticket_id : $e['ticket_id'];
                }, $merged_activities);

                // If this activity doesn't exist in the array then set it.
                if (!in_array($user_activity->ticket_id, $ticket_ids)) {
                    $merged_activities[] = $user_activity;
                } else {
                    // Append the message to the previously set message if it is already set.
                    $merged_activities[array_search($user_activity->ticket_id, $ticket_ids)]->appendMessage($user_activity->message);
                }
            }

            $full_report[] = array(
                'user' => Users::getUser($user),
                'activities' => $merged_activities,
            );
        }

        return $full_report;
    }

    /**
     * Get the Report options.
     * @return bool
     */
    private static function loadOptions()
    {
        // Get the options from session
        $options = Options::get();

        if (!empty($options)) {
            // Set the args
            static::$users = $options['users'];
            static::$projects = $options['projects'];
            return true;
        }
        return false;
    }

    /**
     * Set the start time for the report.
     * @param array $params
     */
    private static function setStartTime($params)
    {
        // Check if it is set first
        if (!empty($params['datetime'])) {
            static::$start_time = strtotime($params['datetime']);

        // Default start time
        } else {
            // Get the default time from the config and set it
            $default_time = static::$di['config']['date']['time'];
            static::$start_time = strtotime($default_time);
        }
    }

}