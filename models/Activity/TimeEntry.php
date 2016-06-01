<?php

namespace UnfuddleReport\Models\Activity;

use UnfuddleReport\Models\Activity;


class TimeEntry extends Activity
{

    /**
     * Comment constructor.
     * @param $project_id
     * @param $belongs_to
     * @param $time_entry
     */
    public function __construct($project_id, $belongs_to, $time_entry)
    {

        // Set the project id
        $this->project_id = $project_id;

        // Set who this comment belongs to
        $this->belongs_to = $belongs_to;

        // Get the Ticket Number
        $this->parent_ticket_number = $time_entry->ticket_number;

        // Get and save the ticket
        $this->ticket = $this->getParentTicket();

        // Set the message
        $this->message = '<strong>[' . date('m/d/Y', strtotime($time_entry->created_at)) . ']:</strong> (' . $time_entry->hours . 'Hrs) ' . htmlentities($time_entry->description);

    }

}