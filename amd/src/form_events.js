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

 define(['jquery', 'block_lsuxe/xe_lib'],
    function($, XELib) {
    'use strict';
    return {
        /**
         * Fetch the token for the current selected URL. Store in temp sessionStorage
         *
         * @param null
         * @return void
         */
        getTokenReady: function () {
            // Check to see if this is the first time landing or not.
            var url = $('#id_available_moodle_instances option:selected').text();

            XELib.getTokenForURL(url).then(function (response) {
                if (response.success == true) {
                    sessionStorage.setItem("currentToken", response.data);
                    sessionStorage.setItem("currentUrl", url);
                } else {
                    // TODO: Send Notification to user that token is crap
                }
            });
        },

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
         * Moodle removes any changed option/select elements. In order to maintain
         * the data push data into hidden some that are in the form's page.
         * @param string name of the tag to be changed
         * @param string the value to insert
         * @return resolved data
         */
        setHiddenValue: function (tag, value) {
            $('input[name='+tag+']').val(value);
        },

        /**
         * Register all the events for the Mappings Form Page.
         * @return void
         */
        registerMappingEvents: function() {
            var that = this,
                form_select = $("#id_srccourseshortname");

            form_select.change(function() {

                if (form_select.val()) {
                    // change invokes any change so only make an ajax call if there is value
                    that.getGroupData({
                        'courseid': form_select.val(),
                        'coursename': $( "#id_srccourseshortname option:selected" ).text()
                    },).then(function (response) {
                        // if the text is disabled then use select
                        if (response.count == 1) {
                            // Single entry so let's update the text field
                            $('#id_srccoursegroupnameselect').val(response.data.groupname);
                            $('#id_srccoursegroupname').val(response.data.groupname);

                        } else if (response.count > 1) {
                            // Multiple groups, so let's unhide the select
                            $('#id_srccoursegroupnameselect').empty();
                            var first_choice = "";
                            for (let i in response.data) {
                                // This is to store the first select and to be used.
                                if (first_choice == "") {
                                    first_choice = {
                                        groupid: response.data[i].groupid,
                                        groupname: response.data[i].groupname
                                    };
                                }
                                $('#id_srccoursegroupnameselect')
                                    .append($("<option></option>")
                                    .attr("value", response.data[i].groupid)
                                    .text(response.data[i].groupname));
                            }

                            // Now that it's been populated, set the hidden elements to match the first
                            // select option.
                            that.setHiddenValue('srccoursegroupname', first_choice.groupname);
                            that.setHiddenValue('srccoursegroupid', first_choice.groupid);
                        } else {
                            // TODO: The count is neither 1 or greate than 1 so no groups?
                            // display no groups.
                        }

                    });
                } else {
                    // if there is no value in the course name then clear out the group name.
                    $('#id_srccoursegroupnameselect').empty();
                    $('#id_srccoursegroupnametext').text();
                    $('#id_srccoursegroupnameselect')
                        .append($("<option></option>")
                        .attr("value", 0)
                        .text("Please search for a course first"));
                }
            });

            // Any changes to the group element, update the hidden.
            $("#id_srccoursegroupnameselect").change(function() {
                var new_value = $(this).find("option:selected").attr('value');
                var new_text = $(this).find("option:selected").text();
                that.setHiddenValue('srccoursegroupname', new_text);
                that.setHiddenValue('srccoursegroupid', new_value);
            });

            // Register events on the moodles form.
            // onChange event for the URL selector
            $('select#id_available_moodle_instances').on('change', function() {
                that.getTokenReady();
            });
        },

        /**
         * These are registered events being loaded that are NOT being called from mustache templates
         * @param null
         * @return resolved data
         */
        registerEvents: function () {

            // Let's not load all events, just what we need.
            if (sessionStorage.getItem('xe_form') == "mappings" && sessionStorage.getItem('xe_viewform') == "true") {
                // Register events on the mappings form.
                this.registerMappingEvents();

            } else if (sessionStorage.getItem('xe_form') == "moodles" && sessionStorage.getItem('xe_viewform') == "true") {
                // Register events on the mappings form.
                console.log("Need to complete this at some point rather than loading from template");
                // TODO: move from template to here.
            }
        },

        init: function () {
            this.registerEvents();
            this.getTokenReady();
        }
    };
});