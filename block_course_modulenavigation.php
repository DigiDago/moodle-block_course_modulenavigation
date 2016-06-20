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
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/course/lib.php');
require_once($CFG->dirroot.'/course/format/lib.php');

/**
 * Course contents block generates a table of course contents based on the
 * section descriptions
 */
class block_course_modulenavigation extends block_base {

    /**
     * Initializes the block, called by the constructor
     */
    public function init() {
        $this->title = get_string('pluginname', 'block_course_modulenavigation');
    }

    /**
     * Amend the block instance after it is loaded
     */
    public function specialization() {
        if (!empty($this->config->blocktitle)) {
            $this->title = $this->config->blocktitle;
        } else {
            $this->title = get_string('config_blocktitle_default', 'block_course_modulenavigation');
        }
    }

    /**
     * Which page types this block may appear on
     * @return array
     */
    public function applicable_formats() {
        return array('site-index' => true, 'course-view-*' => true);
    }

    /**
     * Returns the navigation
     *
     * @return navigation_node The navigation object to display
     */
    protected function get_navigation() {
        $this->page->navigation->initialise();
        return clone($this->page->navigation);
    }

    /**
     * Populate this block's content object
     * @return stdClass block content info
     */
    public function get_content() {
        global $DB;
        if (!is_null($this->content)) {
            return $this->content;
        }

        $selected = optional_param('section', null, PARAM_INT);
        $intab = optional_param('dtab', null, PARAM_TEXT);

        $this->content = new stdClass();
        $this->content->footer = '';
        $this->content->text   = '';

        if (empty($this->instance)) {
            return $this->content;
        }

        $format = course_get_format($this->page->course);
        $course = $format->get_course(); // Needed to have numsections property available.

        if (!$format->uses_sections()) {
            if (debugging()) {
                $this->content->text = get_string('notusingsections', 'block_course_modulenavigation');
            }
            return $this->content;
        }

        if ($format instanceof format_dynamictabs) {
            $sections = $format->tabs_get_sections();
        } else {
            $sections = $format->get_sections();
        }

        if (empty($sections)) {
            return $this->content;
        }

        $context = context_course::instance($course->id);

        if ($format instanceof format_dynamictabs) {
            $course = $format->get_course();
        }
        $modinfo = get_fast_modinfo($course);

        $template = new stdClass();

        $completioninfo = new completion_info($course);

        if ($completioninfo->is_enabled()) {
            $template->completionon = 'completion';
        }

        $completionok = array(COMPLETION_COMPLETE, COMPLETION_COMPLETE_PASS);

        $thiscontext = context::instance_by_id($this->page->context->id);

        $inactivity = false;
        $myactivityid = 0;
        if ($thiscontext->get_level_name() == get_string('activitymodule')) {
            // Uh-oh we are in a activity.
            $inactivity = true;
            if ($cm = $DB->get_record_sql("SELECT cm.*, md.name AS modname
                                           FROM {course_modules} cm
                                           JOIN {modules} md ON md.id = cm.module
                                           WHERE cm.id = ?", array($thiscontext->instanceid))) {
                $myactivityid = $cm->id;
            }
        }

        $template->inactivity = $inactivity;

        if (count($sections) > 1) {
            $template->hasprevnext = true;
            $template->hasnext = true;
            $template->hasprev = true;
        }

        $courseurl = new moodle_url('/course/view.php', array('id' => $course->id));
        $template->courseurl = $courseurl->out();
        $sectionnums = array();
        foreach ($sections as $section) {
            $sectionnums[] = $section->section;
        }

        foreach ($sections as $section) {
            $i = $section->section;
            if ($i > $course->numsections) {
                break;
            }
            if (!$section->uservisible) {
                continue;
            }

            if (!empty($section->name)) {
                $title = format_string($section->name, true, array('context' => $context));

            } else {
                $summary = file_rewrite_pluginfile_urls($section->summary, 'pluginfile.php', $context->id, 'course',
                    'section', $section->id);
                $summary = format_text($summary, $section->summaryformat, array('para' => false, 'context' => $context));
                $title = $format->get_section_name($section);
            }

            $thissection = new stdClass();
            $thissection->number = $i;
            $thissection->title = $title;
            $thissection->url = $format->get_view_url($section);
            $thissection->selected = false;

            if ($i == $selected && !$inactivity) {
                $thissection->selected = true;
            }

            $thissection->modules = array();
            if (!empty($modinfo->sections[$i])) {
                foreach ($modinfo->sections[$i] as $modnumber) {
                    $module = $modinfo->cms[$modnumber];
                    if ($module->modname == 'label' || $module->modname == 'url') {
                        continue;
                    }
                    if (! $module->uservisible) {
                        continue;
                    }
                    $thismod = new stdClass();

                    if ($inactivity) {
                        if ($myactivityid == $module->id) {
                            $thissection->selected = true;
                            $thismod->active = 'active';
                        }
                    }

                    $thismod->name = $module->name;
                    $thismod->url = $module->url;
                    $hascompletion = $completioninfo->is_enabled($module);
                    if ($hascompletion) {
                        $thismod->completeclass = 'incomplete';
                    }

                    $completiondata = $completioninfo->get_data($module, true);
                    if (in_array($completiondata->completionstate, $completionok)) {
                        $thismod->completeclass = 'completed';
                    }
                    $thissection->modules[] = $thismod;
                }
                $template->sections[] = $thissection;
            }

            if ($thissection->selected) {

                $pn = $this->get_prev_next($sectionnums, $thissection->number);

                $courseurl = new moodle_url('/course/view.php', array('id' => $course->id, 'section' => $i));
                $template->courseurl = $courseurl->out();

                if ($pn->next === false) {
                    $template->hasnext = false;
                }
                if ($pn->prev === false) {
                    $template->hasprev = false;
                }

                $prevurl = new moodle_url('/course/view.php', array('id' => $course->id, 'section' => $pn->prev));
                $template->prevurl = $prevurl->out(false);

                $currurl = new moodle_url('/course/view.php', array('id' => $course->id, 'section' => $thissection->number));
                $template->currurl = $currurl->out(false);

                $nexturl = new moodle_url('/course/view.php', array('id' => $course->id, 'section' => $pn->next));
                $template->nexturl = $nexturl->out(false);
            }
        }
        if ($intab) {
            $template->inactivity = true;
        }
        $template->coursename = $course->fullname;
        $template->config = $this->config;
        $renderer = $this->page->get_renderer('block_course_modulenavigation', 'nav');
        $this->content->text = $renderer->render_nav($template);
        return $this->content;
    }

    /**
     * Function to get the previous and next values in an array
     * @param array array to search
     * @param string
     * @return object $pn with prev and next values.
     */
    private function get_prev_next($array, $current) {
        $pn = new stdClass();

        $hascurrent = $pn->next = $pn->prev = false;

        foreach ($array as $a) {
            if ($hascurrent) {
                $pn->next = $a;
                break;
            }
            if ($a == $current) {
                $hascurrent = true;
            } else {
                if (!$hascurrent) {
                    $pn->prev = $a;
                }
            }
        }
        return $pn;
    }
}