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

 define(['jquery', 'block_lsuxe/xe_lib'],
    function($, XELib) {
    'use strict';
    return {
        /**
         * Get group data from a course.
         * @param {object} the json object sent to the server
         * @return resolved data
         */
        getGroupData: function (params) {
            return XELib.jaxyPromise({
                'call': 'getGroupData',
                'params': params,
                'class': 'router'
            });
        },

        /**
         * These are registered events being loaded that are NOT being called from mustache templates
         * @param null
         * @return resolved data
         */
        registerEvents: function () {
            var that = this,
                form_select = $("#id_srccourseshortname");

            if (localStorage['xe_form'] == "mappings" && localStorage['xe_viewform'] == "true") {
                form_select.change(function() {

                    if (form_select.val()) {
                        // change invokes any change so only make an ajax call if there is value
                        that.getGroupData({
                            'courseid': form_select.val(),
                            'coursename': $( "#id_srccourseshortname option:selected" ).text()
                        },).then(function (response) {
                            // console.log("getGroupData() -> what is the response: ", response);
                            $('#id_srccoursegroupname').val(response.data.groupname);
                        });
                    }
                });
            }
        }
    };
});