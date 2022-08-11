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

namespace block_lsuxe\form;

use block_lsuxe\controllers\form_controller;
require_once($CFG->dirroot . '/blocks/lsuxe/lib.php');

class moodles_form extends \moodleform {

    /*
     * Moodle form definition
     */
    public function definition() {
        
        $mappingsctrl = new form_controller("moodles");
        $helpers = new \lsuxe_helpers();
        $formupdating = false;
        if (isset($this->_customdata->id)) {
            $formupdating = true;
        }

        $mform =& $this->_form;
        // checkmark from here:
        // https://codepen.io/scottloway/pen/zqoLyQ

        $checkmark = ''.
            '<div class="circle-loader">'.
                '<div class="checkmark draw"></div>'.
            '</div>'.
            '<span class="circle-cross-loader">'.
                '<span class="crossmark"></span>'.
            '<span>';
        // --------------------------------
        // Moodle Instance URL.
        $urlgroup = array();
        $urlgroup[] =& $mform->createElement(
            'text',
            'instanceurl',
            get_string('instanceurl', 'block_lsuxe')
        );
        $mform->setType(
            'instanceurl',
            PARAM_TEXT
        );
        if (isset($this->_customdata->url)) {
            $mform->setDefault('instanceurl', $this->_customdata->url);
        }
        // Wrap the checkmark so we can control it for tokens
        $urlconfirmhtml = '<div class="xe_confirm_url">'.$checkmark.'</div';
        $urlgroup[] =& $mform->createElement(
            'html', $urlconfirmhtml
        );
        $mform->addGroup($urlgroup, 'url_group', get_string('instanceurl', 'block_lsuxe'), ' ', false);
        
        // ----------------------------------------------------------------
        // ----------------------------------------------------------------
        // ----------------------------------------------------------------
        // --------------------------------
        // Moodle Instance Token.
        $tokengroup = array();
        $tokengroup[] =& $mform->createElement(
        // $mform->addElement(
            'text',
            'instancetoken',
            get_string('instancetoken', 'block_lsuxe')
        );
        $mform->setType(
            'instancetoken',
            PARAM_TEXT
        );
        if (isset($this->_customdata->token)) {
            $mform->setDefault('instancetoken', $this->_customdata->token);
        }
        // Wrap the checkmark so we can control it for tokens
        $tokenconfirmhtml = '<div class="xe_confirm_token">'.$checkmark.'</div';
        $tokengroup[] =& $mform->createElement(
            'html', $tokenconfirmhtml
        );
        $mform->addGroup($tokengroup, 'token_group', get_string('instancetoken', 'block_lsuxe'), ' ', false);
        // ----------------------------------------------------------------
        // ----------------------------------------------------------------



        // --------------------------------
        // Interval.
        $intervals = $helpers->config_to_array('block_lsuxe_interval_list');
        $select = $mform->addElement(
            'select',
            'defaultupdateinterval',
            get_string('defaultupdateinterval', 'block_lsuxe'),
            $intervals,
            []
        );
        if (isset($this->_customdata->updateinterval)) {
            $select->setSelected($this->_customdata->updateinterval);
        }

        // --------------------------------
        // Token Expiration Date Selector.
        $tokenexpiregroup = array();
        $tokenexpiregroup[] =& $mform->createElement(
            'date_selector',
            'tokenexpiration',
            ''
        );
        if (isset($this->_customdata->tokenexpire) && $this->_customdata->tokenexpire != "0") {
            $mform->setDefault('tokenexpiration', $this->_customdata->tokenexpire);
        }

        // --------------------------------
        // Moodle Instance URL.
        $tokenexpiregroup[] =& $mform->createElement(
            'advcheckbox',
            'enabletokenexpiration',
            get_string('tokenenable', 'block_lsuxe')
        );
        if (isset($this->_customdata->tokenexpire) && $this->_customdata->tokenexpire != "0") {
            $mform->setDefault('enabletokenexpiration', 1);
        }

        $mform->addGroup($tokenexpiregroup, 'tokenexpirationgroup', get_string('tokenexpiration', 'block_lsuxe'), ' ', false);
        // --------------------------------
        // Hidden Elements.
        // For Page control list or view form.
        $mform->addElement('hidden', 'vform');
        $mform->setType('vform', PARAM_INT); 
        $mform->setConstant('vform', 1);

        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);
        if ($formupdating) {
            $mform->setDefault('id', $this->_customdata->id);
        }

        // --------------------------------
        // Buttons!
        // The button can either be Save or Update for the submit action.
        $thissubmitbutton = $formupdating ? get_string('savechanges', 'block_lsuxe') : get_string('saveinstance', 'block_lsuxe');
        $buttons = [
            $mform->createElement('submit', 'send', $thissubmitbutton),
            $mform->createElement('button', 'verifysource', get_string('verifyinstance', 'block_lsuxe'))
        ];

        $mform->addGroup($buttons, 'actions', '&nbsp;', [' '], false);
    }

    /*
     * Moodle form validation
     */
    public function validation($data, $files) {
        $errors = [];

        // Check that we have at least one recipient.
        if (empty($data['instanceurl'])) {
            $errors['instanceurl'] = get_string('instanceurlverify', 'block_lsuxe');
        }

        if (empty($data['instancetoken'])) {
            $errors['instancetoken'] = get_string('instancetokenverify', 'block_lsuxe');
        }

        return $errors;
    }
}