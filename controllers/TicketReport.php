<?php
/**
 * Created by PhpStorm.
 * User: Beazy
 * Date: 5/24/2016
 * Time: 6:23 PM
 */

namespace UnfuddleReport\Controllers;

use UnfuddleReport\Models\ReportTicket as TicketModel;


class TicketReport extends Api
{

    // Tickets Report Endpoint
    const TICKET_REPORT_ENDPOINT = '/api/v1/projects/<project_id>/ticket_reports/dynamic.json?fields-string=number,status,summary,assignee&sort-by=status';

    private static $conditions = 'conditions-string=status-eq-In+Progress|status-eq-Code+Complete|status-eq-Code+Review|status-eq-ReOpened';
    
    private static $projects;
    private static $users;

    /**
     * Retrieve the Ticket Report from api
     *
     * @return array
     */
    public static function getReport()
    {

        // Initiate a project list
        $tickets = array();
        
        // Get the options
        if (!static::loadOptions()) {
            return ['errors' => ['message' => 'The report options were not set properly.']];
        }

        // Get the projects
        $projects = Projects::getProjectList();

        try {
            foreach ($projects as $project) {

                // Skip if the project is not in the request settings.
                if (!in_array($project->id, self::$projects)) continue;

                // Set the project id in tickets as an array
                $tickets[$project->id] = array(
                    'name' => $project->name,
                    'tickets' => array()
                );

                // Build the api url
                $api_url = str_ireplace(['<project_id>'], [$project->id], static::TICKET_REPORT_ENDPOINT);

                // Append to the api_url
                if (!empty(self::$conditions)) {
                    $api_url .= '&' . self::$conditions;
                }

                // Get the tickets from the api
                $ticket_report = static::get($api_url);

                // Make sure the ticket report is set
                if (!empty($ticket_report)) {

                    foreach ($ticket_report->groups[0]->tickets as $ticket) {

                        // Skip if not assigned to someone in report options
                        if (!in_array($ticket->assignee_id, self::$users)) continue;

                        // Get a Ticket Model from api
                        $tickets[$project->id]['tickets'][] = new TicketModel(static::getBaseUrl(), $project->id, $ticket);
                    }

                }

            }

        } catch (\Exception $e) {
            echo $e->getMessage() . PHP_EOL;
        }

        // Return the array
        return $tickets;
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

}