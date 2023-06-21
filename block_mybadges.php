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

use core_external\util as external_util;

/**
 * Main file of recent badges plugin. Displays recently awarded badges.
 *
 * @package    block_mybadges
 * @copyright  2023 Matthew Davidson
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(dirname(__FILE__).'/lib.php');

/**
 * Displays recent badges
 */
class block_mybadges extends block_base {

    public $description;

    function init() {
        $this->title = get_string('pluginname', 'block_mybadges');
        $this->description = get_string('defaultdescription', 'block_mybadges');
    }

    function instance_allow_multiple() {
        return true;
    }

    function has_config() {
        return true;
    }

    function instance_allow_config() {
        return true;
    }

    function applicable_formats() {
        return array(
                'admin' => false,
                'site-index' => true,
                'course-view' => true,
                'mod' => false,
                'my' => true
        );
    }

    function specialization() {
        if (isset($this->config->title)) {
            $this->title = $this->title = format_string($this->config->title, true, ['context' => $this->context]);
        } else {
            $this->title = get_string('pluginname', 'block_mybadges');
        }

        if (isset($this->config->description)) {
            $this->description = $this->description = format_string($this->config->description, true, ['context' => $this->context]);
        } else {
            $this->description = get_string('defaultdescription', 'block_mybadges');
        }
    }

    function get_content() {
        global $USER, $COURSE, $CFG;

        if ($this->content !== null) {
            return $this->content;
        }

        if (empty($this->config)) {
            $this->config = new stdClass();
        }

        // Number of course badges to display.
        if (!isset($this->config->numberofcoursebadges)) {
            $this->config->numberofcoursebadges = 6;
        }

        // Number of system badges to display.
        if (!isset($this->config->numberofsystembadges)) {
            $this->config->numberofsystembadges = 6;
        }

        // Size of badge icons.
        if (!isset($this->config->iconsize)) {
            $this->config->iconsize = 'small';
        }

        // Size of badge icons.
        if (!isset($this->config->allownames)) {
            $this->config->allownames = 0;
        }

        // Create empty content.
        $this->content = new stdClass();
        $this->content->text = '';

        if (empty($CFG->enablebadges)) {
            $this->content->text .= get_string('badgesdisabled', 'badges');
            return $this->content;
        }

        $courseid = $this->page->course->id;
        if ($courseid == SITEID) {
            $courseid = null;
        }

        if (get_config('block_mybadges')->allowedmodus != 'onlysystem'
            and $this->config->numberofcoursebadges > 0
            and $coursebadges = block_mybadges_get_issued_badges($courseid, $this->config->numberofcoursebadges)
            and $COURSE->id != SITEID) {

            $output = $this->page->get_renderer('block_mybadges');
            $description = $this->config->description ?: get_string('defaultdescription', 'block_mybadges');
            $this->content->text .= html_writer::tag('div', $description, array('class' => 'my-badges-description'));
            $this->content->text .= $output->mybadges_print_badges_list($coursebadges, $USER->id, $courseid,
                $this->config->iconsize, $this->config->allownames);

        }

        if (get_config('block_mybadges')->allowedmodus != 'onlycourse'
            and $this->config->numberofsystembadges > 0
            and $systembadges = block_mybadges_get_issued_badges(SITEID, $this->config->numberofsystembadges)) {

            $output = $this->page->get_renderer('block_mybadges');
            $this->content->text .= html_writer::tag('div', get_string('latestsystembadges', 'block_mybadges'),
                array('class' => 'my-badges-latestsystembadges'));
            $this->content->text .= $output->mybadges_print_badges_list($systembadges, $USER->id, SITEID,
                $this->config->iconsize, $this->config->allownames);

        }

        return $this->content;
    }
    function _self_test() {
        return true;
      }
}
