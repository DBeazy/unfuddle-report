<?php

namespace UnfuddleReport\Models;


class Ticket
{
    
    public $number;
    public $description;
    public $status;
    public $assignee;
    public $link;

    /**
     * User constructor.
     * @param $base_url
     * @param User $user
     * @param $api_ticket
     */
    public function __construct($base_url, User $user, $api_ticket)
    {

        // Set the number
        if (!empty($api_ticket->number)) {
            $this->number = $api_ticket->number;
        }

        // Set the Description
        if (!empty($api_ticket->description)) {
            $this->description = $api_ticket->summary;
        }

        // Set the Status
        if (!empty($api_ticket->status)) {
            $this->status = $api_ticket->status;
        }

        // Set the Assignee
        $this->assignee = $user;

        // Set the Link
        if (!empty($api_ticket->project_id) && !empty($api_ticket->number)) {
            $this->link = $base_url . '/#a/projects/' . $api_ticket->project_id . '/tickets/by_number/' . $api_ticket->number;
        }

    }
    
}
