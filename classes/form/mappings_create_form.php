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
 * @package    block_lsuxe Cross Enrollment
 * @copyright  2008 onwards Louisiana State University
 * @copyright  2008 onwards David Lowe
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_lsuxe\form;

// require_once($CFG->libdir . '/formslib.php');

class mappings_create_form extends \moodleform {

    /*
     * Moodle form definition
     */
    public function definition() {
        // element_name, type, contents, attr_collections, attributes = array()
        $mform =& $this->_form;

        $mform->addElement(
            'text',
            'srccourseshortname',
            get_string('srccourseshortname', 'block_lsuxe'),
        );
        $mform->setType(
            'srccourseshortname',
            PARAM_TEXT
        );

        $mform->addElement(
            'text',
            'srccoursegroupname',
            get_string('srccoursegroupname', 'block_lsuxe'),
        );
        $mform->setType(
            'srccoursegroupname',
            PARAM_TEXT
        );
        // ----------------
        $moodleinstances = array(
            'moodle.lsu.edu',
            'cemoodle.online.lsu.edu',
            'learn.lsuagcenter.com',
            'mycourses.lsue.edu'
        );
        $mform->addElement(
            'select',
            'available_moodle_instances',
            '',
            $moodleinstances,
            []
        );

        // ----------------
        $mform->addElement(
            'text',
            'destcourseshortname',
            get_string('destcourseshortname', 'block_lsuxe'),
        );
        $mform->setType(
            'destcourseshortname',
            PARAM_TEXT
        );
        // ----------------
        $mform->addElement(
            'text',
            'destcoursegroupname',
            get_string('destcoursegroupname', 'block_lsuxe'),
        );
        $mform->setType(
            'destcoursegroupname',
            PARAM_TEXT
        );

        // ----------------
        $intervals = array(
            'Monthly',
            'Weekly',
            'Daily',
            'Hourly',
            'ASAP',
        );
        $mform->addElement(
            'select',
            'courseupdateinterval',
            '',
            $intervals,
            []
        );
        // Buttons!
        $buttons = [
            $mform->createElement('submit', 'send', get_string('savemapping', 'block_lsuxe')),
            $mform->createElement('button', 'verifysource', get_string('verifysrccourse', 'block_lsuxe')),
            $mform->createElement('button', 'verifydest', get_string('verifydestcourse', 'block_lsuxe')),
        ];

        $mform->addGroup($buttons, 'actions', '&nbsp;', [' '], false);
    }

    /*
     * Moodle form validation
     */
    public function validation($data, $files) {
        $errors = [];

        // srccourseshortname
        // srccoursegroupname
        // available_moodle_instances
        // destcourseshortname
        // destcoursegroupname
        // courseupdateinterval
        // Check that we have at least one recipient.
        if (empty($data['srccourseshortname'])) {
            $errors['srccourseshortname'] = get_string('srccourseshortnameverify', 'block_lsuxe');
        }

        if (empty($data['srccoursegroupname'])) {
            $errors['srccoursegroupname'] = get_string('srccoursegroupnameverify', 'block_lsuxe');
        }

        if (empty($data['destcourseshortname'])) {
            $errors['destcourseshortname'] = get_string('destcourseshortnameverify', 'block_lsuxe');
        }

        if (empty($data['destcoursegroupname'])) {
            $errors['destcoursegroupname'] = get_string('destcoursegroupnameverify', 'block_lsuxe');
        }

        return $errors;
    }

    
}
