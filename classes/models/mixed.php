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

namespace block_lsuxe\models;

/**
 * Mixed functions to retrieve info from the DB.
 */
class mixed {

    // public function __construct() {
    // }

    /**
     * Retrieve basic info about the course and it's group information.
     * @param  object containing the course id and name
     * @return array
     */
    public function getCourseGroup($params) {
        global $DB;
        
        $courseid = isset($params->courseid) ? $params->courseid : null;
        $coursename = isset($params->coursename) ? $params->coursename : null;
        
        $coursedata = $DB->get_record_sql(
            'SELECT c.id, c.idnumber, c.shortname, g.id as groupid, g.name as groupname
            FROM mdl_course c, mdl_groups g
            WHERE c.id = g.courseid AND c.id = ?',
            array($courseid)
        );
        return $coursedata;
    }
}
