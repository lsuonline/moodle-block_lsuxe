<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Cross Enrollment Tool
 *
 * @package    block_lsuxe
 * @copyright  2008 onwards Louisiana State University
 * @copyright  2008 onwards David Lowe
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class router {

    /**
     * Construct the form to work with the persistents.
     *
     * @param string  the name of the object/persistent we are working with
     */
    // public function __construct() {
    // }

    /**
     * Retrieve basic info about the course and it's group information.
     * @param  object containing the course id and name
     * @return array
     */
    public function getGroupData($params) {

        $fuzzy = new \block_lsuxe\models\mixed();
        $data = $fuzzy->getCourseGroup($params);

        return array(
            "success" => true,
            // use msg if you want a message returned for notifications.
            // "msg": "Data was found",
            "data" => array(
                "courseid" => $data->id,
                "idnumber" => $data->idnumber,
                "shortname" => $data->shortname,
                "groupid" => $data->groupid,
                "groupname" => $data->groupname,
            )
        );
    }
}
