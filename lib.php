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
 * @package    block_lsuxe
 * @copyright  2008 onwards Louisiana State University
 * @copyright  2008 onwards David Lowe, Robert Russo
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();
// require_once('../../config.php');

class lsuxe_helpers {


    // Redirects.
    /**
     * Convenience wrapper for redirecting to moodle URLs
     *
     * @param  string  $url
     * @param  array   $urlparams   array of parameters for the given URL
     * @param  int     $delay        delay, in seconds, before redirecting
     * @return (http redirect header)
     */
    public function redirect_to_url($url, $urlparams = [], $delay = 2) {
        $moodleurl = new \moodle_url($url, $urlparams);

        redirect($moodleurl, '', $delay);
    }

    /**
     * Function to grab current enrollments for future use.
     *
     * @return @array of objects
     */
    public static function get_current_enrollments() {
        global $DB, $CFG;

        // LSU UES Specific enrollemnt / unenrollment data.
        $lsql = 'SELECT CONCAT(u.id, "_", c.id, "_", g.id) AS "xeid",
                u.id AS "userid",
                c.id AS "sourcecourseid",
                c.shortname AS "sourcecourseshortname",
                g.id AS "sourcegroupid",
                g.name AS "sourcegroupname",
                u.username AS "username",
                u.email AS "email",
                u.idnumber AS "idnumber",
                u.firstname AS "firstname",
                u.lastname AS "lastname",
                u.alternatename AS "alternamtename",
                stu.status AS "status",
                "student" AS "role",
                xem.url AS "destmoodle",
                xem.token AS "usertoken",
                xemm.destcourseid AS "destcourseid",
                xemm.destcourseshortname AS "destshortname",
                xemm.destgroupid AS "destgroupid",
                CONCAT(xemm.destgroupprefix, " ", xemm.groupname) AS "destgroupname",
                ue.timestart AS "timestart",
                ue.timeend AS "timeend"
            FROM {course} c
                INNER JOIN {block_lsuxe_mappings} xemm ON xemm.courseid = c.id
                INNER JOIN {block_lsuxe_moodles} xem ON xem.id = xemm.destmoodleid
                INNER JOIN {enrol_ues_sections} sec ON sec.idnumber = c.idnumber
                INNER JOIN {enrol_ues_courses} cou ON cou.id = sec.courseid
                INNER JOIN {enrol_ues_students} stu ON stu.sectionid = sec.id
                INNER JOIN {user} u ON u.id = stu.userid
                INNER JOIN {enrol} e ON e.courseid = c.id
                    AND e.enrol = "ues"
                INNER JOIN {user_enrolments} ue ON ue.enrolid = e.id
                    AND ue.userid = u.id
                INNER JOIN {groups} g ON g.courseid = c.id
                    AND g.id = xemm.groupid
                    AND g.name = xemm.groupname
                    AND g.name = CONCAT(cou.department, " ", cou.cou_number, " ", sec.sec_number)
                INNER JOIN {groups_members} gm ON gm.groupid = g.id AND u.id = gm.userid
            WHERE sec.idnumber IS NOT NULL
                AND sec.idnumber <> ""
                AND xemm.destcourseid IS NOT NULL
                AND xemm.destgroupid IS NOT NULL
                AND UNIX_TIMESTAMP() > xemm.starttime
                AND UNIX_TIMESTAMP() < xemm.endtime

            UNION

            SELECT CONCAT(u.id, "_", c.id, "_", g.id) AS "xeid",
                u.id AS "userid",
                c.id AS "sourcecourseid",
                c.shortname AS "sourcecourseshortname",
                g.id AS "sourcegroupid",
                g.name AS "sourcegroupname",
                u.username AS "username",
                u.email AS "email",
                u.idnumber AS "idnumber",
                u.firstname AS "firstname",
                u.lastname AS "lastname",
                u.alternatename AS "alternamtename",
                stu.status AS "status",
                "editingteacher" AS "role",
                xem.url AS "destmoodle",
                xem.token AS "usertoken",
                xemm.destcourseid AS "destcourseid",
                xemm.destcourseshortname AS "destshortname",
                xemm.destgroupid AS "destgroupid",
                CONCAT(xemm.destgroupprefix, " ", xemm.groupname) AS "destgroupname",
                ue.timestart AS "timestart",
                ue.timeend AS "timeend"
            FROM {course} c
                INNER JOIN {block_lsuxe_mappings} xemm ON xemm.courseid = c.id
                INNER JOIN {block_lsuxe_moodles} xem ON xem.id = xemm.destmoodleid
                INNER JOIN {enrol_ues_sections} sec ON sec.idnumber = c.idnumber
                INNER JOIN {enrol_ues_courses} cou ON cou.id = sec.courseid
                INNER JOIN {enrol_ues_teachers} stu ON stu.sectionid = sec.id
                INNER JOIN {user} u ON u.id = stu.userid
                INNER JOIN {enrol} e ON e.courseid = c.id
                    AND e.enrol = "ues"
                INNER JOIN {user_enrolments} ue ON ue.enrolid = e.id
                    AND ue.userid = u.id
                INNER JOIN {groups} g ON g.courseid = c.id
                    AND g.id = xemm.groupid
                    AND g.name = xemm.groupname
                    AND g.name = CONCAT(cou.department, " ", cou.cou_number, " ", sec.sec_number)
                INNER JOIN {groups_members} gm ON gm.groupid = g.id AND u.id = gm.userid
            WHERE sec.idnumber IS NOT NULL
                AND sec.idnumber <> ""
                AND xemm.destcourseid IS NOT NULL
                AND xemm.destgroupid IS NOT NULL
                AND UNIX_TIMESTAMP() > xemm.starttime
                AND UNIX_TIMESTAMP() < xemm.endtime';

        // Generic Moodle enrollment / suspension data.
        $gsql = 'SELECT CONCAT(u.id, "_", c.id, "_", g.id) AS "xeid",
                u.id AS "userid",
                c.id AS "sourcecourseid",
                c.shortname AS "sourcecourseshortname",
                g.id AS "sourcegroupid",
                g.name AS "sourcegroupname",
                u.username AS "username",
                u.email AS "email",
                u.idnumber AS "idnumber",
                u.firstname AS "firstname",
                u.lastname AS "lastname",
                u.alternatename AS "alternamtename",
                IF(ue.status = 0, "enrolled", "unenrolled") AS "status",
                mr.shortname AS "role",
                xem.url AS "destmoodle",
                xem.token AS "usertoken",
                xemm.destcourseid AS "destcourseid",
                xemm.destcourseshortname AS "destshortname",
                xemm.destgroupid AS "destgroupid",
                CONCAT(xemm.destgroupprefix, " ", xemm.groupname) AS "destgroupname",
                ue.timestart AS "timestart",
                ue.timeend AS "timeend"
            FROM {course} c
                INNER JOIN {block_lsuxe_mappings} xemm ON xemm.courseid = c.id
                INNER JOIN {block_lsuxe_moodles} xem ON xem.id = xemm.destmoodleid
                INNER JOIN {enrol} e ON e.courseid = c.id
                INNER JOIN {user_enrolments} ue ON ue.enrolid = e.id
                INNER JOIN {user} u ON u.id = ue.userid
                INNER JOIN {role_assignments} mra ON mra.userid = ue.userid
                    AND mra.userid = u.id
                INNER JOIN {role} mr ON mra.roleid = mr.id
                INNER JOIN {context} ctx ON mra.contextid = ctx.id
                    AND ctx.instanceid = c.id
                    AND ctx.contextlevel = "50"
                INNER JOIN {groups} g ON g.courseid = c.id
                INNER JOIN {groups_members} gm ON gm.groupid = g.id
                    AND u.id = gm.userid
            WHERE xemm.destcourseid IS NOT NULL
                AND xemm.destgroupid IS NOT NULL
                AND UNIX_TIMESTAMP() > xemm.starttime
                AND UNIX_TIMESTAMP() < xemm.endtime';

        // Check to see if we're forcing Moodle enrollment.
        $ues = isset($CFG->xeforceenroll) == 0 ? true : false;

        // Based on the config and if we're using ues, use the appropriate SQL.
        $sql = $ues && self::is_ues() ? $lsql : $gsql;

        // Get the enrollment / unenrollment data.
        $enrolls = $DB->get_records_sql($sql);

        // Return the data.
        return $enrolls;
    }


    /**
     * Function to count records in the UES section table.
     *
     * @return @bool
     */
    public static function is_ues() {
        global $DB;

        // Instantiate the DB manager.
        $dbman = $DB->get_manager();

        // Set the UES table name.
        $uestable = 'enrol_ues_sections';

        // Check to see if UES is installed.
        $uesinstalled = $dbman->table_exists($uestable);

        // Get a count of records in the UES sections table.
        $uescount = $uesinstalled ? $DB->count_records($uestable) : 0;

        // Determines if we're using UES or not.
        $isues = $uescount > 0 ? true : false;

        // Return the appropriate value.
        return $isues;
    }

    public static function xe_write_destcourse() {
        global $DB;

        $sql = 'SELECT xemm.id AS xemmid,
                   xem.url AS "destmoodle",
                   xem.token AS "usertoken",
                   xemm.destcourseshortname AS "destshortname"
               FROM {block_lsuxe_moodles} xem
                   INNER JOIN {block_lsuxe_mappings} xemm ON xemm.destmoodleid = xem.id
               WHERE xemm.destcourseid IS NULL
                   AND UNIX_TIMESTAMP() > xemm.starttime
                   AND UNIX_TIMESTAMP() < xemm.endtime';

        $datas = $DB->get_records_sql($sql);

        if($datas) {
            foreach ($datas as $data) {
                $pageparams = [
                    'wstoken' => $data->usertoken,
                    'wsfunction' => 'core_course_get_courses_by_field',
                    'moodlewsrestformat' => 'json',
                    'field' => 'shortname',
                    'value' => $data->destshortname,
                ];

                $defaults = array(
                    CURLOPT_URL => 'https://' . $data->destmoodle . '/webservice/rest/server.php',
                    CURLOPT_HEADER => 0,
                    CURLOPT_RETURNTRANSFER => TRUE,
                    CURLOPT_TIMEOUT => 4,
                    CURLOPT_POST => false,
                    CURLOPT_POSTFIELDS => $pageparams,
                );

                $ch = curl_init();
                curl_setopt_array($ch, $defaults);

                $returndata[] = curl_exec($ch);
                curl_close($ch);

                $destcourseid = json_decode($returndata[0], true)['courses'][0]['id'];

                $dataobject = [
                    'id' => $data->xemmid,
                    'destcourseid' => $destcourseid,
                ];

                $writeout = $DB->update_record('block_lsuxe_mappings', $dataobject, $bulk=false);
            }
        }
        return isset($errors) ? $errors : true;
    }


    public static function xe_write_destgroup() {
        global $DB;

        $sql = 'SELECT xemm.id AS xemmid,
                   xem.url AS "destmoodle",
                   xem.token AS "usertoken",
                   xemm.destcourseid AS "destcourseid",
                   xemm.destgroupprefix AS "destgroupprefix",
                   xemm.groupname AS "groupname"
               FROM {block_lsuxe_moodles} xem
                   INNER JOIN {block_lsuxe_mappings} xemm ON xemm.destmoodleid = xem.id
               WHERE xemm.destgroupid IS NULL
                   AND xemm.destcourseid IS NOT NULL
                   AND UNIX_TIMESTAMP() > xemm.starttime
                   AND UNIX_TIMESTAMP() < xemm.endtime';

        $datas = $DB->get_records_sql($sql);

        if($datas) {
            foreach ($datas as $data) {
                $gpageparams = [
                    'wstoken' => $data->usertoken,
                    'wsfunction' => 'core_group_get_course_groups',
                    'moodlewsrestformat' => 'json',
                    'courseid' => $data->destcourseid,
                ];

                $upageparams = [
                    'wstoken' => $data->usertoken,
                    'wsfunction' => 'core_group_create_groups',
                    'moodlewsrestformat' => 'json',
                    'groups[0][courseid]' => $data->destcourseid,
                    'groups[0][name]' => $data->destgroupprefix . " " . $data->groupname,
                    'groups[0][description]' => "From " . $data->destgroupprefix,
                ];

                $gdefaults = array(
                    CURLOPT_URL => 'https://' . $data->destmoodle . '/webservice/rest/server.php',
                    CURLOPT_HEADER => 0,
                    CURLOPT_RETURNTRANSFER => TRUE,
                    CURLOPT_TIMEOUT => 4,
                    CURLOPT_POST => false,
                    CURLOPT_POSTFIELDS => $gpageparams,
                );

                $udefaults = array(
                    CURLOPT_URL => 'https://' . $data->destmoodle . '/webservice/rest/server.php',
                    CURLOPT_HEADER => 0,
                    CURLOPT_RETURNTRANSFER => TRUE,
                    CURLOPT_TIMEOUT => 4,
                    CURLOPT_POST => false,
                    CURLOPT_POSTFIELDS => $upageparams,
                );

                $ch = curl_init();
                curl_setopt_array($ch, $gdefaults);

                $returndata[] = curl_exec($ch);
                curl_close($ch);

                $returnedgroups = json_decode($returndata[0], true);

                foreach ($returnedgroups as $returnedgroup) {
                    $destgroupnamexp = $data->destgroupprefix . " " . $data->groupname;
                    $destgroupname = $returnedgroup['name'];

                    if ($destgroupnamexp == $destgroupname) {
                        $destgroupid = $returnedgroup['id'];
                        break;
                    } else {
                        $destgroupid = null;
                    }
                }

                if (isset($destgroupid)) {
                    $dataobject = [
                        'id' => $data->xemmid,
                        'destgroupid' => $destgroupid,
                    ];
                    $writeout = $DB->update_record('block_lsuxe_mappings', $dataobject, $bulk=false);
                } else {
                $ch2 = curl_init();
                curl_setopt_array($ch2, $udefaults);

                $returndata2[] = curl_exec($ch2);
                curl_close($ch2);
                }
            }
        }
        return isset($errors) ? $errors : true;
    }
}
