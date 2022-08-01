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

define([
    'jquery',
    'core/ajax',
], function($, Ajax) {
    'use strict';

    return {
        /**
         * AJAX method to access the external services for Cross Enrollment
         *
         * @param {object} The request arguments
         * Format to make calls is:
         *      'call': [the function name],
                'params': data you want to pass in JSON format,
                'class': [class name AND file name, should match]
         * @return {promise} Resolved with an array of the calendar events
         */
        XEAjax: function(data_chunk) {
            var promiseObj = new Promise(function(resolve, reject) {
                // console.log("XEAajax() -> START, let's Poke the Server");
                var send_this = [{
                    methodname: 'block_lsuxe_XEAjax',
                    args: {
                        datachunk: data_chunk,
                    }
                }];
                Ajax.call(send_this)[0].then(function(results) {
                    // console.log("XEAjax() -> SUCCESS, what is result: ", results);
                    resolve(JSON.parse(results.data));
                }).catch(function(ev) {
                    console.log("XEAjax() -> JAXY Fail :-(");
                    console.log("XEAjax() -> JAXY Fail going to reject: ", ev);
                    reject(ev);
                });
            });
            return promiseObj;
        },
    };
});
