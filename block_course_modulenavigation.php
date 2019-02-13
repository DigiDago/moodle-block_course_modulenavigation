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
 * Course contents block generates a table of course contents based on the section descriptions.
 * @package    block_course_modulenavigation
 * @copyright  2019 Pimenko <contact@pimenko.com> <pimenko.com>
 * @author     Sylvain Revenu | Nick Papoutsis | Bas Brands | DigiDago
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once( $CFG->dirroot . '/course/lib.php' );
require_once( $CFG->dirroot . '/course/format/lib.php' );

/**
 * Define the block course modulenavigation.
 * @package    block_course_modulenavigation
 * @copyright  2019 Pimenko <contact@pimenko.com> <pimenko.com>
 * @author     Sylvain Revenu | Nick Papoutsis | Bas Brands | DigiDago
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_course_modulenavigation extends block_base {

    /**
     * Initializes the block, called by the constructor.
     */
    public function init() {
        $this->title = get_string(
            'pluginname',
            'block_course_modulenavigation'
        );
    }

    /**
     *  Allow parameters in admin settings
     */
    public function has_config() {
        return true;
    }

    /**
     * Amend the block instance after it is loaded.
     */
    public function specialization() {
        if (!empty($this->config->blocktitle)) {
            $this->title = $this->config->blocktitle;
        } else {
            $this->title = get_string(
                'config_blocktitle_default',
                'block_course_modulenavigation'
            );
        }
    }

    /**
     * Which page types this block may appear on.
     * @return array
     */
    public function applicable_formats() {
        return [
            'site-index'    => true,
            'course-view-*' => true
        ];
    }

    /**
     * Returns the navigation.
     * @return navigation_node The navigation object to display
     */
    protected function get_navigation() {
        $this->page->navigation->initialise();
        return clone( $this->page->navigation );
    }

    /**
     * Populate this block's content object.
     * @return stdClass|stdObject
     * @throws coding_exception
     * @throws dml_exception
     * @throws moodle_exception
     */
    public function get_content() {
        global $DB, $PAGE;

        if (!is_null($this->content)) {
            return $this->content;
        }

        $selected = optional_param(
            'section',
            null,
            PARAM_INT
        );
        $intab    = optional_param(
            'dtab',
            null,
            PARAM_TEXT
        );

        $this->content         = new stdClass();
        $this->content->footer = '';
        $this->content->text   = '';

        if (empty($this->instance)) {
            return $this->content;
        }

        if ($PAGE->pagelayout == 'admin') {
            return $this->content;
        }

        $format = course_get_format($this->page->course);
        $course = $format->get_course(); // Needed to have numsections property available.

        if (!$format->uses_sections()) {
            if (debugging()) {
                $this->content->text = get_string(
                    'notusingsections',
                    'block_course_modulenavigation'
                );
            }
            return $this->content;
        }

        if (( $format instanceof format_digidagotabs ) || ( $format instanceof format_horizontaltabs )) {
            // Don't show the menu in a tab.
            if ($intab) {
                return $this->content;
            }
            // Only show the block inside activities of courses.
            if ($this->page->pagelayout == 'incourse') {
                $sections = $format->tabs_get_sections();
            }
        } else {
            $sections = $format->get_sections();
        }

        if (empty($sections)) {
            return $this->content;
        }

        $context = context_course::instance($course->id);

        $modinfo = get_fast_modinfo($course);

        $template = new stdClass();

        $completioninfo = new completion_info($course);

        if ($completioninfo->is_enabled()) {
            $template->completionon = 'completion';
        }

        $completionok = [
            COMPLETION_COMPLETE,
            COMPLETION_COMPLETE_PASS
        ];

        $thiscontext = context::instance_by_id($this->page->context->id);

        $inactivity   = false;
        $myactivityid = 0;

        if ($thiscontext->get_level_name() == get_string('activitymodule')) {
            // Uh-oh we are in a activity.
            $inactivity = true;
            if ($cm = $DB->get_record_sql(
                "SELECT cm.*, md.name AS modname
                                           FROM {course_modules} cm
                                           JOIN {modules} md ON md.id = cm.module
                                           WHERE cm.id = ?",
                [ $thiscontext->instanceid ]
            )) {
                $myactivityid = $cm->id;
            }
        }

        if ($format instanceof format_digidagotabs || $format instanceof format_horizontaltabs) {
            $coursesections = $DB->get_records(
                'course_sections',
                [ 'course' => $course->id ]
            );
            $mysection      = 0;
            foreach ($coursesections as $cs) {
                $csmodules = explode(
                    ',',
                    $cs->sequence
                );
                if (in_array(
                    $myactivityid,
                    $csmodules
                )) {
                    $mysection = $cs->id;
                }
            }

            if ($mysection) {
                if (( $format instanceof format_digidagotabs && $DB->get_records(
                            'format_digidagotabs_tabs',
                            [
                                'courseid'  => $course->id,
                                'sectionid' => $mysection
                            ]
                        ) ) || ( $format instanceof format_horizontaltabs && $DB->get_records(
                            'format_horizontaltabs_tabs',
                            [
                                'courseid'  => $course->id,
                                'sectionid' => $mysection
                            ]
                        ) )) {
                    // This is a module inside a tab of the Dynamic tabs course format.
                    // Prevent showing of this menu.
                    return $this->content;
                }
            }
        }

        $template->inactivity = $inactivity;

        if (count($sections) > 1) {
            $template->hasprevnext = true;
            $template->hasnext     = true;
            $template->hasprev     = true;
        }

        $courseurl           = new moodle_url(
            '/course/view.php',
            [ 'id' => $course->id ]
        );
        $template->courseurl = $courseurl->out();
        $sectionnums         = [];
        foreach ($sections as $section) {
            $sectionnums[] = $section->section;
        }
        foreach ($sections as $section) {
            $i = $section->section;
            if (!$section->uservisible) {
                continue;
            }

            if (!empty($section->name)) {
                $title = format_string(
                    $section->name,
                    true,
                    [ 'context' => $context ]
                );
            } else {
                $summary = file_rewrite_pluginfile_urls(
                    $section->summary,
                    'pluginfile.php',
                    $context->id,
                    'course',
                    'section',
                    $section->id
                );
                $summary = format_text(
                    $summary,
                    $section->summaryformat,
                    [
                        'para'    => false,
                        'context' => $context
                    ]
                );
                $title   = $format->get_section_name($section);
            }

            $thissection           = new stdClass();
            $thissection->number   = $i;
            $thissection->title    = $title;
            $thissection->url      = $format->get_view_url($section);
            $thissection->selected = false;

            if (get_config(
                    'block_course_modulenavigation',
                    'toggleclickontitle'
                ) == 2) {
                // Display the menu.
                $thissection->collapse = true;
            } else {
                // Go to link.
                $thissection->collapse = false;
            }

            if (get_config(
                    'block_course_modulenavigation',
                    'togglecollapse'
                ) == 2) {
                $thissection->selected = true;
            }

            // Show only titles.
            if (get_config(
                    'block_course_modulenavigation',
                    'toggletitles'
                ) == 2) {
                // Show only titles.
                $thissection->onlytitles = true;
            } else {
                // Show  titles and contents.
                $thissection->onlytitles = false;
            }

            if ($i == $selected && !$inactivity) {
                $thissection->selected = true;
            }

            $thissection->modules = [];
            if (!empty($modinfo->sections[$i])) {
                foreach ($modinfo->sections[$i] as $modnumber) {
                    $module = $modinfo->cms[$modnumber];
                    if (( get_config(
                                'block_course_modulenavigation',
                                'toggleshowlabels'
                            ) == 1 ) && ( $module->modname == 'label' )) {
                        continue;
                    }
                    if (!$module->uservisible || !$module->visible || !$module->visibleoncoursepage) {
                        continue;
                    }
                    $thismod = new stdClass();

                    if ($inactivity) {
                        if ($myactivityid == $module->id) {
                            $thissection->selected = true;
                            $thismod->active       = 'active';
                        }
                    }

                    $thismod->name = format_string(
                        $module->name,
                        true,
                        [ 'context' => $context ]
                    );
                    $thismod->url  = $module->url;
                    if ($module->modname == 'label') {
                        $thismod->url   = '';
                        $thismod->label = 'true';
                    }
                    $hascompletion = $completioninfo->is_enabled($module);
                    if ($hascompletion) {
                        $thismod->completeclass = 'incomplete';
                    }

                    $completiondata = $completioninfo->get_data(
                        $module,
                        true
                    );
                    if (in_array(
                        $completiondata->completionstate,
                        $completionok
                    )) {
                        $thismod->completeclass = 'completed';
                    }
                    $thissection->modules[] = $thismod;
                }
                $thissection->hasmodules = ( count($thissection->modules) > 0 );
                $template->sections[]    = $thissection;
            }

            if ($thissection->selected) {

                $pn = $this->get_prev_next(
                    $sectionnums,
                    $thissection->number
                );

                $courseurl           = new moodle_url(
                    '/course/view.php',
                    [
                        'id'      => $course->id,
                        'section' => $i
                    ]
                );
                $template->courseurl = $courseurl->out();

                if ($pn->next === false) {
                    $template->hasnext = false;
                }
                if ($pn->prev === false) {
                    $template->hasprev = false;
                }

                $prevurl           = new moodle_url(
                    '/course/view.php',
                    [
                        'id'      => $course->id,
                        'section' => $pn->prev
                    ]
                );
                $template->prevurl = $prevurl->out(false);

                $currurl           = new moodle_url(
                    '/course/view.php',
                    [
                        'id'      => $course->id,
                        'section' => $thissection->number
                    ]
                );
                $template->currurl = $currurl->out(false);

                $nexturl           = new moodle_url(
                    '/course/view.php',
                    [
                        'id'      => $course->id,
                        'section' => $pn->next
                    ]
                );
                $template->nexturl = $nexturl->out(false);
            }
        }
        if ($intab) {
            $template->inactivity = true;
        }
        $template->coursename = $course->fullname;
        $template->config     = $this->config;
        $renderer             = $this->page->get_renderer(
            'block_course_modulenavigation',
            'nav'
        );
        $this->content->text  = $renderer->render_nav($template);
        return $this->content;
    }

    /**
     * Function to get the previous and next values in an array.
     *
     * @param $array
     * @param $current
     *
     * @return stdClass
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