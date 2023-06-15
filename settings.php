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
 * Global settings for recent badges plugin.
 *
 * @package    block_mybadges
 * @copyright  2023 Matthew Davidson
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

if ($hassiteconfig) {

    $ADMIN->add('blocksettings', new admin_category('block_mybadges_folder',
                get_string('pluginname', 'block_mybadges')));

    $settings = new admin_settingpage('block_mybadges', get_string('configuration', 'block_mybadges'));

    $options = array('onlycourse' => get_string('onlycourse', 'block_mybadges'),
                     'onlysystem' => get_string('onlysystem', 'block_mybadges'),
                     'courseandsystem' => get_string('courseandsystem', 'block_mybadges'));

    $settings->add(new admin_setting_configselect('block_mybadges/allowedmodus',
        get_string('modus', 'block_mybadges'),
        get_string('modusinfo', 'block_mybadges'), 'courseandsystem', $options));

    $settings->add(new admin_setting_configcheckbox('block_mybadges/allownames',
        get_string('allownamesglobal', 'block_mybadges'),
        get_string('allownamesinfoglobal', 'block_mybadges'), 0));

    $ADMIN->add('block_mybadges_folder', $settings);

    $ADMIN->add('block_mybadges_folder', new admin_externalpage('block_mybadges_about',
                get_string('about', 'block_mybadges'),
                new moodle_url('/blocks/mybadges/about.php')));

    $settings = null;
}
