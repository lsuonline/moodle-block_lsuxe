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
    public static function xe_current_enrollments() {
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
                u.alternatename AS "alternatename",
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
                u.alternatename AS "alternatename",
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
                u.alternatename AS "alternatename",
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

    /**
     * Function to grab the destination course id and write it locally.
     *
     * @return @array of objects
     */
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

                unset($returndata);
                $returndata = curl_exec($ch);

                curl_close($ch);

                $destcourseid = json_decode($returndata, true)['courses'][0]['id'];

                $dataobject = [
                    'id' => $data->xemmid,
                    'destcourseid' => $destcourseid,
                ];

                $writeout = $DB->update_record('block_lsuxe_mappings', $dataobject, $bulk=false);
            }
        }
        return isset($errors) ? $errors : true;
    }

    /**
     * Function to grab destination group id if it exsits.
     * Write the existing group id locally.
     * If the destination group is not present, create it.
     *
     * @return true
     */
    public static function xe_write_destgroup() {
        global $DB;

        // Build the SQL to get the appropriate data for the webservice.
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

        // Actually get the data.
        $datas = $DB->get_records_sql($sql);

        // Just a check to make sure we have stuff.
        if($datas) {
            foreach ($datas as $data) {

                // Set the group check page params.
                $gpageparams = [
                    'wstoken' => $data->usertoken,
                    'wsfunction' => 'core_group_get_course_groups',
                    'moodlewsrestformat' => 'json',
                    'courseid' => $data->destcourseid,
                ];

                // Set the group creation page params.
                $upageparams = [
                    'wstoken' => $data->usertoken,
                    'wsfunction' => 'core_group_create_groups',
                    'moodlewsrestformat' => 'json',
                    'groups[0][courseid]' => $data->destcourseid,
                    'groups[0][name]' => $data->destgroupprefix . " " . $data->groupname,
                    'groups[0][description]' => "From " . $data->destgroupprefix,
                ];

                // Set the group check defaults.
                $gdefaults = array(
                    CURLOPT_URL => 'https://' . $data->destmoodle . '/webservice/rest/server.php',
                    CURLOPT_HEADER => 0,
                    CURLOPT_RETURNTRANSFER => TRUE,
                    CURLOPT_TIMEOUT => 4,
                    CURLOPT_POST => false,
                    CURLOPT_POSTFIELDS => $gpageparams,
                );

                // Set the group creation defaults.
                $udefaults = array(
                    CURLOPT_URL => 'https://' . $data->destmoodle . '/webservice/rest/server.php',
                    CURLOPT_HEADER => 0,
                    CURLOPT_RETURNTRANSFER => TRUE,
                    CURLOPT_TIMEOUT => 4,
                    CURLOPT_POST => false,
                    CURLOPT_POSTFIELDS => $upageparams,
                );

                // Create the curl handler.
                $ch = curl_init();
                // Set the curl options.
                curl_setopt_array($ch, $gdefaults);

                // Run the curl handler and store the returned data.
                unset($returndata);
                $returndata = curl_exec($ch);

                // Close the curl handler.
                curl_close($ch);

                // Decode the returned data.
                $returnedgroups = json_decode($returndata, true);

                // Loop through the returned groups and try to match the intended group name.
                foreach ($returnedgroups as $returnedgroup) {
                    // Build the intended group name.
                    $destgroupnamexp = $data->destgroupprefix . " " . $data->groupname;
                    // Set the actual remote group name.
                    $destgroupname = $returnedgroup['name'];

                    // If we have a match, store the destination group id and exit the loop.
                    if ($destgroupnamexp == $destgroupname) {
                        $destgroupid = $returnedgroup['id'];
                        break;
                    } else {
                        $destgroupid = null;
                    }
                }

                // If we have a destination group id stored in memory.
                if (isset($destgroupid)) {
                    // Build the data object for writing to the local DB.
                    $dataobject = [
                        'id' => $data->xemmid,
                        'destgroupid' => $destgroupid,
                    ];
                    // Write it locally.
                    $writeout = $DB->update_record('block_lsuxe_mappings', $dataobject, $bulk=false);
                // If we DO NOT have a matching destination group.
                } else {
                    // Set up another curl handler.
                    $ch2 = curl_init();
                    // Set its options.
                    curl_setopt_array($ch2, $udefaults);

                    // Execute the curl handler and store the returned data.
                    unset($returndata);
                    $returndata2 = curl_exec($ch2);

                    // Close the curl handler.
                    curl_close($ch2);

                    // Decode the json data. 
                    $destgroupid2 = json_decode($returndata2, true)[0]['id'];

                    // Another sanity check to make sure it's set before we write it.
                    if (isset($destgroupid2)) {
                        // Set the data object for writing to our DB.
                        $dataobject2 = [
                            'id' => $data->xemmid,
                            'destgroupid' => $destgroupid2,
                        ];
                        // Update the record.
                        $writeout2 = $DB->update_record('block_lsuxe_mappings', $dataobject2, $bulk=false);
                    }
                }
            }
        }
        return true;
    }

    public static function xe_get_users() {
        global $CFG, $DB;

        $lsql = 'SELECT u.id AS "userid",
                u.username AS "username",
                u.email AS "email",
                u.idnumber AS "idnumber",
                u.firstname AS "firstname",
                u.lastname AS "lastname",
                u.alternatename AS "alternatename",
                u.auth AS "auth",
                xem.url AS "destmoodle",
                xem.token AS "usertoken"
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

            SELECT u.id AS "userid",
                u.username AS "username",
                u.email AS "email",
                u.idnumber AS "idnumber",
                u.firstname AS "firstname",
                u.lastname AS "lastname",
                u.alternatename AS "alternatename",
                u.auth AS "auth",
                xem.url AS "destmoodle",
                xem.token AS "usertoken"
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
                AND UNIX_TIMESTAMP() < xemm.endtime

            GROUP BY userid';

        $gsql = 'u.id AS "userid",
                u.username AS "username",
                u.email AS "email",
                u.idnumber AS "idnumber",
                u.firstname AS "firstname",
                u.lastname AS "lastname",
                u.alternatename AS "alternatename",
                u.auth AS "auth",
                xem.url AS "destmoodle",
                xem.token AS "usertoken"
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
        $users = $DB->get_records_sql($sql);

        // Return the data.
        return $users;
    }

    public static function xe_remote_user_helper() {

        $users = self::xe_get_users();
        foreach ($users as $user) {

                // Set the group check page params.
                unset($gpageparams);
                $gpageparams = [
                    'wstoken' => $user->usertoken,
                    'wsfunction' => 'core_user_get_users',
                    'moodlewsrestformat' => 'json',
                    'criteria[0][key]' => 'username',
                    'criteria[0][value]' => $user->username,
                ];

                // Set the group creation page params.
                $upageparams = [
                    'wstoken' => $user->usertoken,
                    'wsfunction' => 'core_user_update_users',
                    'moodlewsrestformat' => 'json',
                    'users[0][username]' => $user->username,
                    'users[0][email]' => $user->email,
                    'users[0][auth]' => $user->auth,
                    'users[0][firstname]' => $user->firstname,
                    'users[0][lastname]' => $user->lastname,
                    'users[0][alternatename]' => $user->alternatename,
                    'users[0][email]' => $user->email,
                    'users[0][idnumber]' => $user->idnumber,
                ];

                // Set the group check defaults.
                unset($gdefaults);
                $gdefaults = array(
                    CURLOPT_URL => 'https://' . $user->destmoodle . '/webservice/rest/server.php',
                    CURLOPT_HEADER => 0,
                    CURLOPT_RETURNTRANSFER => TRUE,
                    CURLOPT_TIMEOUT => 4,
                    CURLOPT_POST => false,
                    CURLOPT_POSTFIELDS => $gpageparams,
                );

                // Set the group creation defaults.
                $udefaults = array(
                    CURLOPT_URL => 'https://' . $user->destmoodle . '/webservice/rest/server.php',
                    CURLOPT_HEADER => 0,
                    CURLOPT_RETURNTRANSFER => TRUE,
                    CURLOPT_TIMEOUT => 4,
                    CURLOPT_POST => false,
                    CURLOPT_POSTFIELDS => $upageparams,
                );

                // Create the curl handler.
                $ch = curl_init();
                // Set the curl options.
                curl_setopt_array($ch, $gdefaults);

                // Run the curl handler and store the returned data.
                unset($returndata);
                $returndata = curl_exec($ch);

                // Close the curl handler.
                curl_close($ch);

                // Decode the returned data.
                $returnedusers        = json_decode($returndata, true);

                // Set up the remote user object.
                $ruser                = new stdClass();
                $ruser->username      = $returnedusers['users'][0]['username'];
                $ruser->email         = $returnedusers['users'][0]['email'];
                $ruser->idnumber      = $returnedusers['users'][0]['idnumber'];
                $ruser->firstname     = $returnedusers['users'][0]['firstname'];
                $ruser->lastname      = $returnedusers['users'][0]['lastname'];
                $ruser->alternatename = isset($returnedusers['users'][0]['alternatename']) ? $returnedusers['users'][0]['alternatename'] : null;
                $ruser->auth          = $returnedusers['users'][0]['auth'];

                // Set up the local user object.
                $luser                = new stdClass();
                $luser->username      = $user->username;
                $luser->email         = $user->email;
                $luser->idnumber      = $user->idnumber;
                $luser->firstname     = $user->firstname;
                $luser->lastname      = $user->lastname;
                $luser->alternatename = $user->alternatename;
                $luser->auth          = $user->auth;

                if ($luser->username == $ruser->username) {
                    echo"<br>Local user <strong>$luser->username</strong> matches remote user <strong>$ruser->username</strong>.";
                    // Check to see if all the user details match.
                    if ($luser == $ruser) {
                        echo"<br>the local and remote user objects match entirely.";
                        // Do nothing.
                    } else {
                        echo"<br>Something in the user object does not match, update the remote user.";
                        // Update the user.
                    }
                } else {
                  echo"<br>User $luser->username not found on remote system, create them.";
                  // Create the user.
                }

                $remoteusers[] = $returnedusers['users'][0];
        }
        return $remoteusers;
    }
}
