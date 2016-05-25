<?php
/**
 * Created by PhpStorm.
 * User: Beazy
 * Date: 5/24/2016
 * Time: 6:39 PM
 */

namespace UnfuddleReport\Models;


class ReportTicket
{

    public $number;
    public $summary;
    public $status;
    public $link;
    
    /**
     * User constructor.
     * @param $base_url
     * @param $project_id
     * @param $api_ticket
     */
    public function __construct($base_url, $project_id, $api_ticket)
    {

        // Set the number
        if (!empty($api_ticket->number)) {
            $this->number = $api_ticket->number;
        }

        // Set the Summary
        if (!empty($api_ticket->summary)) {
            $this->summary = $api_ticket->summary;
        }

        // Set the Status
        if (!empty($api_ticket->status)) {
            $this->status = $api_ticket->status;
        }

        // Set the Link
        if (!empty($project_id) && !empty($api_ticket->number)) {
            $this->link = $base_url . '/a#/projects/' . $project_id . '/tickets/by_number/' . $api_ticket->number;
        }

    }
}