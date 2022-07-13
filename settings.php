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

defined('MOODLE_INTERNAL') || die();
// Set the string for use later.
$fn = new lang_string('foldername', 'block_lsuxe');

// Create the folder / submenu.
$ADMIN->add('blocksettings', new admin_category('blockxefolder', $fn));

// Create the settings block.
$settings = new admin_settingpage($section, get_string('settings'));

// Make sure only admins see this one.
if ($ADMIN->fulltree) {

    /*
    $settings->add(
        new admin_setting_heading(
            'xeheader',
            get_string('headerconfig', 'block_newblock'),
            get_string('descconfig', 'block_newblock')
        )
    );

    $settings->add(
        new admin_setting_configcheckbox(
            'newblock/foo',
            get_string('labelfoo', 'block_newblock'),
            get_string('descfoo', 'block_newblock'),
            '0'
        )
    );
    */
}

// LSUXE Settings --------------------------------------------------------------------------
$settings->add(
    new admin_setting_heading(
        'xe_moodle_instances_main_title',
        get_string('xe_moodle_instances_main_title', 'block_lsuxe'),
        ''
    )
);

$settings->add(
    new admin_setting_configtextarea(
        'xe_moodle_instances_list',
        get_string('xe_moodle_instances_list', 'block_lsuxe'),
        'List of Moodle instances',
        'moodle.lsu.edu, lsuonline.moodle.lsu.edu',
        PARAM_TEXT
    )
);

// Add the folder.
$ADMIN->add('blockxefolder', $settings);

// Prevent Moodle from adding settings block in standard location.
$settings = null;

// Set the url for the Cross Enrollment override tool.
$xeoverride = new admin_externalpage(
    'manage_overrides',
    new lang_string('manage_overrides', 'block_lsuxe'),
    "$CFG->wwwroot/blocks/lsuxe/overrides.php"
);


// $context = \context_system::instance();
// Add the link for those who have access.
// if (has_capability('block/xe:admin', $context)) {
//     $ADMIN->add('blockxefolder', $xeoverride);
//     // $ADMIN->add('blockxefolder', $puinvalids);
// }