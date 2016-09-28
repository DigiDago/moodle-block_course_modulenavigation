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
 * @package    block_course_modulenavigation
 * @copyright  2016 Digidago <contact@digidago.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {

    // Toggle all on/off.
    // 1 => No.
    // 2 => Yes.

    $name = 'block_course_modulenavigation/toggleclickonarrow';
    $title = get_string('toggleclickonarrow', 'block_course_modulenavigation');
    $description = get_string('toggleclickonarrow_desc', 'block_course_modulenavigation');
    $default = 2;
    $choices = array(
        1 => get_string('toggleclickonarrow_menu', 'block_course_modulenavigation'),
        2 => get_string('toggleclickonarrow_page', 'block_course_modulenavigation'),
    );
    $settings->add(new admin_setting_configselect($name, $title, $description, $default, $choices));
}