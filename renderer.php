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
 * Output renderer for recent badges plugin.
 *
 * @package    block_mybadges
 * @copyright  2023 Matthew Davidson
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/badgeslib.php');
require_once($CFG->libdir.'/tablelib.php');

/**
 * Renders the badges block.
 *
 * @copyright  2023 Matthew Davidson
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_mybadges_renderer extends plugin_renderer_base {
    /**
     * Print out list of badges in different formats.
     *
     * @param array $badges All collected badges.
     * @param int $userid Current viewing user ID.
     * @param int $courseid Moodle course ID or SITEID.
     * @param object $config All config variables.
     * @return string HTML of badge list.
     */
    public function mybadges_print_badges_list($badges, $userid, $courseid, $config) {
        global $DB, $CFG;

        $earnedby = get_string('user', 'block_mybadges');
        $overalluseritem = '';
        $items = array();
        if (!empty($badges)) {
            foreach ($badges as $badge) {
                if ($badge->type == BADGE_TYPE_SITE) {
                    $context = context_system::instance();
                } else {
                    $context = context_course::instance($badge->courseid);
                }

                $name = $badge->name;
                $imageurl = moodle_url::make_pluginfile_url($context->id, 'badges', 'badgeimage', $badge->id, '/', 'f1', false);
                $image = html_writer::empty_tag('img', array('src' => $imageurl, 'class' => 'badge-image'));

                if (!empty($badge->dateexpire) && $badge->dateexpire < time()) {
                    $image .= $this->output->pix_icon('i/expired',
                        get_string('expireddate', 'badges', userdate($badge->dateexpire)), 'moodle',
                            array('class' => 'expireimage'));
                    $name .= ' (' . get_string('expired', 'badges') . ')';
                }

                $badgeparams = ($courseid == SITEID) ? array('type' => 1) : array('type' => 2, 'id' => $courseid);
                $badgeurl = new moodle_url('/badges/view.php', $badgeparams);

                $title = html_writer::tag('span', $name, array('class' => 'badge-title'));
                if ($config->iconsize == 'small') {
                    $item = html_writer::tag('div', $image.' '.$title, array('class' => 'badge-item'));
                } else {
                    $item = html_writer::tag('div', $image, array('class' => 'badge-item')).
                        html_writer::tag('div', $title, array('class' => 'badge-item'));
                }
                $badgeitem = html_writer::link($badgeurl, $item, array('title' => $name));

                $useritem = '';
                if ($config->allownames &&
                    $courseid != SITEID &&
                    has_capability('moodle/course:viewparticipants', $context)) {

                        $badgeuser = $DB->get_record('user', array('id' => $badge->userid));
                        $username = html_writer::tag('span', fullname($badgeuser), array('class' => 'user-name'));
                        $useritem = $username;

                    if (has_capability('moodle/user:viewdetails', $context) &&
                        has_capability('moodle/course:viewparticipants', $context)) {

                            $userurl = new moodle_url('/user/view.php', array('id' => $badge->userid, 'course' => $courseid));
                            $useritem = html_writer::link($userurl, $username, array('title' => $username));
                    }
                }
                // Decide whether to show the name of badge earner or not.
                if ($config->iconsize == "smalloverlapping" || $config->iconsize == "bigoverlapping") {
                    if ($config->allownames) {
                        if ($config->onlymybadges == "singleuser") {
                            $overalluseritem = $earnedby . $useritem;
                        } else {
                            $overalluseritem = get_string('allparticipants', 'block_mybadges');
                        }
                        $overalluseritem = html_writer::tag('div', $overalluseritem, array('class' => 'my-badges-combined'));
                    }
                    $items[] = $badgeitem;
                } else {
                    $useritem = empty($useritem) ? '' : $earnedby . $useritem;
                    $items[] = $badgeitem . $useritem;
                }
            }
        }

        return html_writer::alist($items, array('class' => 'my-badges-' . $config->iconsize)) . $overalluseritem;
    }
}
