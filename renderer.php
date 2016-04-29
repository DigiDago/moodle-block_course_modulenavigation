<?php
// This file is part of The Course Module Navigation Block
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
 * @author     Bas Brands
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class block_course_modulenavigation_nav_renderer extends plugin_renderer_base {
    public function render_nav($template) {
        if (isset($template->config->onesection) && ($template->config->onesection == 1)) {
            return $this->render_from_template('block_course_modulenavigation/coursenav_onesection', $template);
        } else {
            return $this->render_from_template('block_course_modulenavigation/coursenav', $template);
        }
    }
}