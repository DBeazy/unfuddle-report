<?php
namespace UnfuddleReport\Models;


class Comment extends Activity
{

    /**
     * Comment constructor.
     * @param $project_id
     * @param $belongs_to
     * @param $comment
     */
    public function __construct($project_id, $belongs_to, $comment)
    {

        // Set the project id
        $this->project_id = $project_id;

        // Set who this comment belongs to
        $this->belongs_to = $belongs_to;

        // Get the Ticket Number
        $this->parent_ticket_number = $comment->ticket_number;

        // Get and save the ticket
        $this->ticket = $this->getParentTicket();

        // Set the message
        $this->message = '<strong>[' . date('m/d/Y', strtotime($comment->created_at)) . ']:</strong>' . htmlentities($comment->body);

    }

}