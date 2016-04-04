<?php
namespace UnfuddleReport\Models;


class Project
{
    public $id;
    public $name;

    /**
     * User constructor.
     * @param $api_project
     */
    public function __construct($api_project)
    {

        // The api_project can sometimes be an array or a StdCls
        if (is_array($api_project)) {

            // Set the ID
            if (!empty($api_project['id'])) {
                $this->id = $api_project['id'];
            }

            // Set the Name
            if (!empty($api_project['title'])) {
                $this->name = $api_project['title'];
            }

        } else {

            // Set the ID
            if (!empty($api_project->id)) {
                $this->id = $api_project->id;
            }

            // Set the Name
            if (!empty($api_project->title)) {
                $this->name = $api_project->title;
            }

        }

    }

}
