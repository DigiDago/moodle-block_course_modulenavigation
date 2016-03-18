<?php
// This file is part of The Course Navigation Block
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
 * @package    block_course_navigation
 * @copyright  2016 Bas Brands <bas@sonsbeekmedia.nl>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/course/lib.php');
require_once($CFG->dirroot.'/course/format/lib.php');

/**
 * Course contents block generates a table of course contents based on the
 * section descriptions
 */
class block_course_navigation extends block_base {


    public $titles;
    /**
     * Initializes the block, called by the constructor
     */
    public function init() {
        $this->title = get_string('pluginname', 'block_course_navigation');
    }

    /**
     * Amend the block instance after it is loaded
     */
    public function specialization() {
        if (!empty($this->config->blocktitle)) {
            $this->title = $this->config->blocktitle;
        } else {
            $this->title = get_string('config_blocktitle_default', 'block_course_navigation');
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
        // Initialise (only actually happens if it hasn't already been done yet)
        $this->page->navigation->initialise();
        return clone($this->page->navigation);
    }

    /**
     * Populate this block's content object
     * @return stdClass block content info
     */
    public function get_content() {

        if (!is_null($this->content)) {
            return $this->content;
        }

        $selected = optional_param('section', null, PARAM_INT);

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
                $this->content->text = get_string('notusingsections', 'block_course_navigation');
            }
            return $this->content;
        }

        $sections = $format->get_sections();

        if (empty($sections)) {
            return $this->content;
        }

        $context = context_course::instance($course->id);

        $text = html_writer::start_tag('ul', array('class' => 'section-list'));

        if ($format instanceof format_dynamictabs) {
            $text .= 'working with dynamic tabs';
            //$text .= print_r($format->get_tabs(), true);
            $course = $format->get_course();
            $modinfo = get_fast_modinfo($course);
        
        }

        $template = new stdClass();

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
                if ($format instanceof format_dynamictabs) {
                    if (preg_match('/\{icon\}/', $title)) {
                        continue;
                    }
                }
            } else {
                $summary = file_rewrite_pluginfile_urls($section->summary, 'pluginfile.php', $context->id, 'course',
                    'section', $section->id);
                $summary = format_text($summary, $section->summaryformat, array('para' => false, 'context' => $context));
                $title = format_string($this->extract_title($summary), true, array('context' => $context));
                if (empty($title)) {
                    $title = $format->get_section_name($section);
                }
            }

            $thissection = new stdClass();
            $thissection->number = $i;
            $thissection->title = $title;
            $thissection->url = $format->get_view_url($section);
            $thissection->selected = false;

            if ($i == $selected) {
                $thissection->selected = true;
            }

            $thissection->modules = array();
            if (!empty($modinfo->sections[$i])) {
                foreach ($modinfo->sections[$i] as $modnumber) {
                    $thissection->modules[] = $modinfo->cms[$modnumber];
                }
            }
            $template->sections[] = $thissection;
        }


        
        $renderer = $this->page->get_renderer('block_course_navigation', 'nav');
        $this->content->text = $renderer->render_nav($template);

        //$this->content->text = '<pre>' . print_r($template, true) . '</pre>';

        return $this->content;
    }


    /**
     * Given a section summary, exctract a text suitable as a section title
     *
     * @param string $summary Section summary as returned from database (no slashes)
     * @return string Section title
     */
    private function extract_title($summary) {
        global $CFG;
        require_once(dirname(__FILE__).'/lib/simple_html_dom.php');

        $node = new simple_html_dom();
        $node->load($summary);
        return $this->node_plain_text($node);
    }


    /**
     * Recursively find the first suitable plaintext from the HTML DOM.
     *
     * Internal private function called only from {@link extract_title()}
     *
     * @param simple_html_dom $node Current root node
     * @return string
     */
    private function node_plain_text($node) {
        if ($node->nodetype == HDOM_TYPE_TEXT) {
            $t = trim($node->plaintext);
            if (!empty($t)) {
                return $t;
            }
        }
        $t = '';
        foreach ($node->nodes as $n) {
            $t = $this->node_plain_text($n);
            if (!empty($t)) {
                break;
            }
        }
        return $t;
    }
}