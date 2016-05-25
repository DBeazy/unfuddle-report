<?php
/**
 * Created by PhpStorm.
 * User: Beazy
 * Date: 5/24/2016
 * Time: 6:23 PM
 */

namespace UnfuddleReport\Controllers;

use UnfuddleReport\Models\ReportTicket as TicketModel;


class TicketReport extends api
{

    // Tickets Report Endpoint
    const TICKET_REPORT_ENDPOINT = '/api/v1/projects/<project_id>/ticket_reports/dynamic.json?fields-string=number,status,summary&sort-by=status';

    private static $conditions = 'conditions-string=status-eq-In+Progress|status-eq-Code+Complete|status-eq-Code+Review|status-eq-Needs+Feedback|status-eq-ReOpened';

    /**
     * Retrieve the Ticket Report from api
     *
     * @return array
     */
    public static function getReport()
    {

        // Initiate a project list
        $tickets = array();

        // Get the projects
        $projects = Projects::getProjectList();

        try {
            foreach ($projects as $project) {

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

}