<?php

namespace UnfuddleReport\Controllers;

use UnfuddleReport\Models\Project;

class Projects extends Api
{

    // Projects Endpoint
    const PROJECTS_ENDPOINT = '/api/v1/projects.json';

    protected static $save_key = 'projects_list';

    /**
     * Retrieve the Project list from api
     *
     * @return array
     */
    public static function getProjectList()
    {

        // Initiate a project list
        $project_list = array();

        try {

            // If there is a saved project list in session, use that
            if (static::has_list()) {
                $projects = static::get_list();
            } else {

                // Get the Projects from the api
                $projects = static::get(static::PROJECTS_ENDPOINT);

                // Save the list of projects in session
                static::save_list($projects);
            }

            // Iterate through the returned Projects
            if (!empty($projects)) {

                foreach ($projects as $project) {
                    $project_list[] = new Project($project);
                }

            }

        } catch (\Exception $e) {
            echo $e->getMessage() . PHP_EOL;
        }

        // Return the array
        return $project_list;
    }

}