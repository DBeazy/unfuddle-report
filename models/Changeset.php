<?php

namespace UnfuddleReport\Models;


class Changeset extends Activity
{

    /**
     * Changeset constructor.
     * @param $project_id
     * @param $belongs_to
     * @param $changeset
     */
    public function __construct($project_id, $belongs_to, $changeset)
    {

        // Set the project id
        $this->project_id = $project_id;
        
        // Set who this comment belongs to
        $this->belongs_to = $belongs_to;

        // Get the Ticket Number
        preg_match('~^\#([0-9]+)~', $changeset->message, $matches);
        if (!empty($matches[1])) {
            $this->parent_ticket_number = $matches[1];
        }

        // Get and save the ticket
        $this->ticket = $this->getParentTicket();

        // Set the message
        $this->message = '<strong>[' . date('m/d/Y', strtotime($changeset->created_at)) . ']:</strong>' . htmlentities($changeset->message);

    }

}
