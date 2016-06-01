<?php

namespace UnfuddleReport\Controllers;

use UnfuddleReport\Models\Activity\Changeset;
use UnfuddleReport\Models\Activity\Comment;
use UnfuddleReport\Models\Activity\TimeEntry;

class Activity extends Api
{

    // Projects Endpoint
    const ACTIVITY_ENDPOINT = '/api/v1/projects/<project_id>/activity.json';

    /**
     * Retrieve the Activity list from api
     *
     * @param array $filters
     * @return array
     */
    public static function getActivityList(array $filters)
    {

        // Initiate a project list
        $activity_list = array();

        // Make sure filters are set
        if (!empty($filters)) {

            // Loop through each project
            foreach ($filters['projects'] as $project_id) {

                // Get the api url
                $api_url = str_ireplace('<project_id>', $project_id, static::ACTIVITY_ENDPOINT);

                // Add the query string
                if (!empty($filters['start_date'])) {
                    $api_url .= '?start_date=' . date('Y/m/d', $filters['start_date']);
                }

                try {

                    // Get the Projects from the api
                    $activities = static::get($api_url);

                    // Iterate through the returned Projects
                    if (!empty($activities)) {

                        foreach ($activities as $activity) {
                            $activity_class = static::setActivity($project_id, $activity, $filters);
                            // If the class is set then we can save it in the list
                            if (!empty($activity_class)) {
                                $activity_list[] = $activity_class;
                            }
                        }

                    }

                } catch (\Exception $e) {
                    echo $e->getMessage() . PHP_EOL;
                }

            }

        }

        // Return the array
        return $activity_list;
    }

    /**
     * 
     * @param $pj_id
     * @param $activity
     * @param array $further_filters
     * @return bool|Changeset|Comment
     */
    private static function setActivity($pj_id, $activity, $further_filters = [])
    {

        // Only save Comment and Changeset
        if (!in_array($activity->record_type, array('Comment', 'Changeset', 'TimeEntry'))) {
            return false;
        }


        // Only save if we are looking for specific people
        if (!in_array($activity->person_id, $further_filters['users'])) {
            return false;
        }

        // Check the timestamp on the activity
        if (strtotime($activity->created_at) < $further_filters['start_date']) {
            return false;
        }
        
        // Set as a new Comment Model
        if ($activity->record_type === 'Comment') {

            // Set the ticket number from the activity
            $activity->record->comment->ticket_number = $activity->ticket_number;

            // Return a comment object
            return new Comment($pj_id, $activity->person_id, $activity->record->comment);

        } else if ($activity->record_type === 'TimeEntry') {

                // Set the ticket number from the activity
                $activity->record->ticket_entry->ticket_number = $activity->ticket_number;

                // Return a comment object
                return new TimeEntry($pj_id, $activity->person_id, $activity->record->ticket_entry);

        } else {
            
            // Return a Changeset object
            return new Changeset($pj_id, $activity->person_id, $activity->record->changeset);
            
        }
        
    }
    
}