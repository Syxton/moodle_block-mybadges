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
 * The library file for recent badges plugin.
 *
 * @package    block_mybadges
 * @copyright  2023 Matthew Davidson
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

use core_external\util as external_util;
require_once(dirname(__FILE__).'/lib.php');

/**
 * Configures and displays the block.
 *
 * @copyright  2023 Matthew Davidson
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_mybadges extends block_base {
    /** @var string optional text that displays inside the block above the badges. */
    public $description;

    /**
     * Initialize the plugin. This method is being called by the parent constructor by default.
     */
    public function init() {
        $this->title = get_string('pluginname', 'block_mybadges');
        $this->description = get_string('defaultdescription', 'block_mybadges');
    }

    /**
     * No need to have multiple blocks to perform the same functionality
     */
    public function instance_allow_multiple() {
        return true;
    }

    /**
     * Has config function.
     *
     * @see block_base::has_config()
     */
    public function has_config() {
        return true;
    }

    /**
     * Allow per instance configurrations.
     *
     * @return boolean
     */
    public function instance_allow_config() {
        return true;
    }

    /**
     * Add custom html attributes to aid with theming and styling
     *
     * @return array
     */
    public function applicable_formats() {
        return array(
                'admin' => false,
                'site-index' => true,
                'course-view' => true,
                'mod' => false,
                'my' => true
        );
    }

    /**
     * Alter the title and descriptions of the instance of the block.
     *
     */
    public function specialization() {
        if (isset($this->config->title)) {
            $this->title = format_string($this->config->title, true, ['context' => $this->context]);
        } else {
            $this->title = get_string('pluginname', 'block_mybadges');
        }

        if (isset($this->config->description)) {
            $this->description = format_string($this->config->description, true, ['context' => $this->context]);
        } else {
            $this->description = get_string('defaultdescription', 'block_mybadges');
        }
    }

    /**
     * Sets up the content of the block for display to the user.
     *
     * @return stdClass The HTML content of the block.
     */
    public function get_content(): stdClass {
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

        if (get_config('block_mybadges')->allowedmodus != 'onlysystem' &&
            $courseid != SITEID &&
            $this->config->numberofcoursebadges > 0 &&
            $coursebadges = block_mybadges_get_issued_badges($courseid, $this->config)) {

            $output = $this->page->get_renderer('block_mybadges');
            $description = $this->config->description ?: get_string('defaultdescription', 'block_mybadges');
            $this->content->text .= html_writer::tag('div', $description, array('class' => 'my-badges-description'));
            $this->content->text .= $output->mybadges_print_badges_list($coursebadges, $USER->id, $courseid, $this->config);
        }

        $systembadges = false;
        if (get_config('block_mybadges')->allowedmodus != 'onlycourse' &&
            $this->config->numberofsystembadges > 0 &&
            $systembadges = block_mybadges_get_issued_badges(SITEID, $this->config)) {

            $output = $this->page->get_renderer('block_mybadges');
            $this->content->text .= html_writer::tag('div', get_string('latestsystembadges', 'block_mybadges'),
                array('class' => 'my-badges-latestsystembadges'));
            $this->content->text .= $output->mybadges_print_badges_list($systembadges, $USER->id, SITEID, $this->config);

        }

        if (!$coursebadges && !$systembadges) {
            $this->content->text = html_writer::tag('div', get_string('nobadgesfound', 'block_mybadges'));
        }
        return $this->content;
    }
}
