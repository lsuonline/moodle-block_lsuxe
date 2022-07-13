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

class moodles_create_form extends \moodleform {

    /*
     * Moodle form definition
     */
    public function definition() {
        // element_name, type, contents, attr_collections, attributes = array()
        $mform =& $this->_form;

        $mform->addElement(
            'text',
            'instanceurl',
            get_string('instanceurl', 'block_lsuxe'),
        );
        $mform->setType(
            'instanceurl',
            PARAM_TEXT
        );
        // ----------------
        $mform->addElement(
            'text',
            'instancetoken',
            get_string('instancetoken', 'block_lsuxe'),
        );
        $mform->setType(
            'instancetoken',
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
        // ----------------
        $mform->addElement(
            'date_time_selector',
            'assesstimestart',
            get_string('from'),
        // array(
        //     'startyear' => 1970, 
        //     'stopyear'  => 2020,
        //     'timezone'  => 99,
        //     'optional'  => false
        // )
        );
        // ----------------

        $mform->addElement(
            'checkbox',
            'ratingtime',
            get_string('tokenenable', 'block_lsuxe')
        );

        // Buttons!
        $buttons = [
            $mform->createElement('submit', 'send', get_string('saveinstance', 'block_lsuxe')),
            $mform->createElement('button', 'verifysource', get_string('verifyinstance', 'block_lsuxe')),
        ];

        $mform->addGroup($buttons, 'actions', '&nbsp;', [' '], false);
    }

    /*
     * Moodle form validation
     */
    public function validation($data, $files) {
        $errors = [];

        // instanceurl
        // instancetoken
        // courseupdateinterval
        // assesstimestart
        // ratingtime
        // Check that we have at least one recipient.
        if (empty($data['instanceurl'])) {
            $errors['instanceurl'] = get_string('no_included_recipients_validation');
        }

        return $errors;
    }


}
