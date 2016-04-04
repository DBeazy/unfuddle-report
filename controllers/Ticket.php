<?php

namespace UnfuddleReport\Controllers;

use UnfuddleReport\Models\Ticket as TicketModel;

class Ticket extends Api
{

    // Ticket Endpoint
    const TICKET_ENDPOINT = '/api/v1/projects/<project_id>/tickets/by_number/<ticket_number>.json';

    /**
     * Retrieve the Ticket from api
     *
     * @param $project_id
     * @param $ticket_number
     * @return array
     */
    public static function getTicket($project_id, $ticket_number)
    {

        // Initiate a project list
        $ticket = array();

        try {

            // Build the api url
            $api_url = str_ireplace(['<project_id>', '<ticket_number>'], [$project_id, $ticket_number], static::TICKET_ENDPOINT);

            // Get the Projects from the api
            $ticket_api = static::get($api_url);

            // Make sure the ticket is set
            if (!empty($ticket_api)) {

                // Get the user from the id on the ticket
                $user = Users::getUser($ticket_api->assignee_id);

                // Get a Ticket Model from api
                $ticket = new TicketModel(static::getBaseUrl(), $user, $ticket_api);

            }

        } catch (\Exception $e) {
            echo $e->getMessage() . PHP_EOL;
        }

        // Return the array
        return $ticket;
    }

}