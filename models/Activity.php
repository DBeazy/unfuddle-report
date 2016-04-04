<?php

namespace UnfuddleReport\Models;

use UnfuddleReport\Controllers\Ticket;

class Activity
{

    public $ticket_id;
    public $ticket;
    public $message;
    public $belongs_to;
    
    protected $parent_ticket_number;
    protected $project_id;

    /**
     * Get the ticket from the api based on the ticket number
     * 
     * @return array|bool
     */
    public function getParentTicket()
    {

        // If either required parameter are not set then return false.
        if (empty($this->parent_ticket_number) || empty($this->project_id)) {
            return false;
        }

        // Set the ticket id
        $this->ticket_id = $this->parent_ticket_number;

        // Return the ticket
        return Ticket::getTicket($this->project_id, $this->parent_ticket_number);

    }

    /**
     * Be able to append to the message by passing the next message
     * @param $message
     */
    public function appendMessage($message)
    {
        $this->message = $this->message . '<br>' . $message;
    }

}