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
 * Edits an instance of recent badges plugin.
 *
 * @package    block_mybadges
 * @copyright  2023 Matthew Davidson
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Form for editing mybadges block instances.
 *
 * @copyright 2023 Matthew Davidson
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_mybadges_edit_form extends block_edit_form {
    /**
     * Form page definitions.
     *
     * @param object $mform Moodle form.
     */
    protected function specific_definition($mform) {
        global $COURSE;

        $mform->addElement('header', 'configheader', get_string('blocksettings', 'block'));

        $mform->addElement('text', 'config_title', get_string('title', 'block_mybadges'));
        $mform->setType('config_title', PARAM_TEXT);

        $mform->addElement('text', 'config_description', get_string('description', 'block_mybadges'));
        $mform->setType('config_description', PARAM_TEXT);

        if (get_config('block_mybadges')->allowedmodus != 'onlysystem' && $COURSE->id != SITEID) {

            $numberofcoursebadges = array();
            for ($i = 0; $i <= 25; $i++) {
                $numberofcoursebadges[$i] = $i;
            }
            $mform->addElement('select', 'config_numberofcoursebadges',
                               get_string('numberofcoursebadges', 'block_mybadges'), $numberofcoursebadges);
            $mform->setDefault('config_numberofcoursebadges', 6);

            if (get_config('block_mybadges')->allownames) {
                $mform->addElement('advcheckbox', 'config_allownames', get_string('allownames', 'block_mybadges'),
                                   get_string('allownamesinfo', 'block_mybadges'), null, array(0, 1));
                $mform->setDefault('config_allownames', 0);
            }
        }

        if (get_config('block_mybadges')->allowedmodus != 'onlycourse') {
            $numberofsystembadges = array();
            for ($i = 0; $i <= 25; $i++) {
                $numberofsystembadges[$i] = $i;
            }
            $mform->addElement('select', 'config_numberofsystembadges',
                               get_string('numberofsystembadges', 'block_mybadges'), $numberofsystembadges);
            $mform->setDefault('config_numberofsystembadges', 6);
        }

        $iconsize = array(
            'small' => get_string('small', 'block_mybadges'),
            'smalloverlapping' => get_string('smalloverlapping', 'block_mybadges'),
            'big' => get_string('big', 'block_mybadges'),
            'bigoverlapping' => get_string('bigoverlapping', 'block_mybadges')
        );
        $mform->addElement('select', 'config_iconsize',
                           get_string('iconsize', 'block_mybadges'), $iconsize);
        $mform->setDefault('config_iconsize', 'small');

        $onlymybadges = array(
            'singleuser' => get_string('singleuser', 'block_mybadges'),
            'everyuser' => get_string('everyuser', 'block_mybadges')
        );
        $mform->addElement('select', 'config_onlymybadges',
                           get_string('onlymybadges', 'block_mybadges'), $onlymybadges);
        $mform->setDefault('config_onlymybadges', 'singleuser');
    }
}
