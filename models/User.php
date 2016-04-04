<?php

namespace UnfuddleReport\Models;


class User
{

    public $id;
    public $first_name;
    public $last_name;

    /**
     * User constructor.
     * @param $api_person
     */
    public function __construct($api_person)
    {

        // The api_person can sometimes be an array or a stdClass
        if (is_array($api_person)) {

            // Set the ID
            if (!empty($api_person['id'])) {
                $this->id = $api_person['id'];
            }

            // Set the First Name
            if (!empty($api_person['first_name'])) {
                $this->first_name = $api_person['first_name'];
            }

            // Set the Last Name
            if (!empty($api_person['last_name'])) {
                $this->last_name = $api_person['last_name'];
            }


        } else {

            // Set the ID
            if (!empty($api_person->id)) {
                $this->id = $api_person->id;
            }

            // Set the First Name
            if (!empty($api_person->first_name)) {
                $this->first_name = $api_person->first_name;
            }

            // Set the Last Name
            if (!empty($api_person->last_name)) {
                $this->last_name = $api_person->last_name;
            }
        }

    }

}
