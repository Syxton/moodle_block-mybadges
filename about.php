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
 * About recent badges plugin
 *
 * @package    block_mybadges
 * @copyright  2023 Matthew Davidson
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(dirname(__FILE__).'/lib.php');

require_login();
$context = context_system::instance();

$pageparams = array();
admin_externalpage_setup('block_mybadges_about');
$siteurl = new moodle_url('/blocks/mybadges/about.php', $pageparams);

$PAGE->set_url($siteurl);
$PAGE->set_context($context);
$PAGE->set_pagelayout('admin');

echo $OUTPUT->header();

// Code for Paypal donation button.
$paypalhtml = '<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_blank">
	<input type="hidden" name="cmd" value="_s-xclick">
	<input type="hidden" name="hosted_button_id" value="E35SWENXMYVGC">
	<input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donate_LG.gif" border="0" name="submit"
        alt="PayPal - The safer, easier way to pay online!">
	<img alt="" border="0" src="https://www.paypalobjects.com/de_DE/i/scr/pixel.gif" width="1" height="1">
	</form>';

$params = array('badgeenrol' => html_writer::link('https://moodle.org/plugins/enrol_badgeenrol', 'Badge Enrolment',
    array('target' => '_blank')),
    'badgepool' => html_writer::link('https://moodle.org/plugins/local_bs_badge_pool', 'Badge Pool', array('target' => '_blank')),
    'badgeladder' => html_writer::link('https://moodle.org/plugins/local_bs_badge_ladder',
        'Badge Ladder', array('target' => '_blank')));

$paypalbox = html_writer::div($paypalhtml, 'donation-button');
$abouttext = html_writer::div(get_string('abouttext', 'block_mybadges', $params), 'about-text');
$donationtext = html_writer::div(get_string('donationtext', 'block_mybadges'), 'donation-text');

$params = array('aboutlink' => html_writer::link('https://moodle.org/plugins/block_mybadges',
    get_string('plugindirectory', 'block_mybadges'), array('target' => '_blank')),
    'aboutmail' => html_writer::link('mailto:moodle@matthiasschwa.be', 'moodle@matthiasschwa.be'));

$aboutfeedback = html_writer::div(get_string('aboutfeedbacktext', 'block_mybadges', $params), 'aboutfeedback-text');

echo $OUTPUT->box($abouttext.$aboutfeedback.$donationtext.$paypalbox, 'about-box');
echo $OUTPUT->footer();
