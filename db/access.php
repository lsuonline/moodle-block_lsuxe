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

$capabilities = array(

    'block/lsuxe:myaddinstance' => array(
        // 'riskbitmask' => RISK_PERSONAL,
        'captype' => 'write',
        'contextlevel' => CONTEXT_SYSTEM,
        'archetypes' => array(
            'manager' => CAP_ALLOW,
            'frontpage' => CAP_PREVENT,
            'user' => CAP_PREVENT
        )
    ),

    'block/lsuxe:addinstance' => array(
        'riskbitmask' => RISK_PERSONAL,
        'captype' => 'write',
        'contextlevel' => CONTEXT_BLOCK,
        'archetypes' => array(
            'manager' => CAP_ALLOW,
            'editingteacher' => CAP_PREVENT,
            'user' => CAP_PREVENT
        ),

        'clonepermissionsfrom' => 'moodle/site:manageblocks'
    ),

    // 'block/xe:admin' => array(
    //     'riskbitmask' => RISK_CONFIG,
    //     'captype' => 'write',
    //     'contextlevel' => CONTEXT_SYSTEM,
    //     'archetypes' => array(
    //         'manager' => CAP_ALLOW,
    //         'frontpage' => CAP_PREVENT,
    //         'user' => CAP_PREVENT
    //     ),
    // )
);
