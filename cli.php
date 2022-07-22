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
 * @copyright  2008 onwards David Lowe
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('CLI_SCRIPT', true);
require_once('../../config.php');
require_once($CFG->dirroot . '/blocks/lsuxe/lib.php');

// Set the start time so we can log how long this takes.
$starttime = microtime(true);

if (lsuxe_helpers::is_ues()) {
   echo("Using UES\n");
} else {
   echo("Normal Enrollment\n");
}

lsuxe_helpers::xe_write_destcourse();

$groups = lsuxe_helpers::xe_get_groups();

lsuxe_helpers::xe_write_destgroups($groups);

$users = lsuxe_helpers::xe_current_enrollments();

$count = 0;
foreach ($users as $user) {
$count++;

$userstarttime = microtime(true);
    $remoteuser = lsuxe_helpers::xe_remote_user_lookup($user);
    if (isset($remoteuser['id'])) {
        $usermatch = lsuxe_helpers::xe_remote_user_match($user, $remoteuser);
        if (!$usermatch) {
            $updateuser = lsuxe_helpers::xe_remote_user_update($user, $remoteuser);
        }
    } else {
       $createduser = lsuxe_helpers::xe_remote_user_create($user);

       $remoteuser = $createduser;
    }
    if ($user->status == 'enrolled') {
        $enrolluser = lsuxe_helpers::xe_enroll_user($user, $remoteuser['id']);
        $enrolgroup = lsuxe_helpers::xe_add_user_to_group($user, $remoteuser['id']);
    } else {
        $enrolluser = lsuxe_helpers::xe_unenroll_user($user, $remoteuser['id']);
    }

    $userelapsedtime = round(microtime(true) - $userstarttime, 3);
    mtrace("\nUser $count took " . $userelapsedtime . " seconds to process.");
}

$elapsedtime = round(microtime(true) - $starttime, 3);
mtrace("\n\nThis entire process took " . $elapsedtime . " seconds.");
