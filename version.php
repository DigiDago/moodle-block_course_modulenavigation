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
 * Course module navigation version.
 *
 * @package    block_course_modulenavigation
 * @copyright  2018 Digidago <contact@digidago.com><www.digidago.com>
 * @author     Sylvain Revenu | Nick Papoutsis | Bas Brands | DigiDago
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

$plugin->component  = 'block_course_modulenavigation';
$plugin->release    = '5.0';
$plugin->version    = 2018110912;
$plugin->requires   = 2018051700; // Moodle 3.5 and above.
$plugin->maturity   = MATURITY_STABLE;
