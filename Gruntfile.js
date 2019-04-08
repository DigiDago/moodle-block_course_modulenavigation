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
/* jshint node: true, browser: false */

/**
 * Grunt config.
 *
 * @package    block_course_modulenavigation
 * @copyright  2019 Pimenko <contact@pimenko.com> <pimenko.com>
 * @author     Sylvain Revenu | Nick Papoutsis | Bas Brands | DigiDago
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
"use strict";

module.exports = function (grunt) {
    // Load contrib tasks.
    grunt.loadNpmTasks("grunt-sass");
    grunt.loadNpmTasks("grunt-contrib-watch");
    grunt.loadNpmTasks("grunt-text-replace");
    grunt.loadNpmTasks("grunt-exec");
    grunt.loadNpmTasks("grunt-stylelint");
    // Import modules.
    let path = require('path');
    const sass = require('node-sass');
    // PHP strings for exec task.
    let moodleroot = path.dirname(path.dirname(__dirname)),
        configfile = '',
        decachephp = '',
        dirrootopt = grunt.option('dirroot') || process.env.MOODLE_DIR || '';

    // Allow user to explicitly define Moodle root dir.
    if ('' !== dirrootopt) {
        moodleroot = path.resolve(dirrootopt);
    }

    configfile = path.join(moodleroot, 'config.php');

    decachephp += 'define(\'CLI_SCRIPT\', true);';
    decachephp += 'require(\'' + configfile + '\');';
    decachephp += 'theme_reset_all_caches();';

    grunt.initConfig({
        sass: {
            // Compile moodle styles.
            options: {
                implementation: sass,
                sourceMap: true
            },
            dist: {
                files: {
                    'styles.css': 'scss/*.scss'
                }
            }
        },
        stylelint: {
            all: ['scss/*.scss']
        },
        exec: {
            decache: {
                cmd: 'php -r "' + decachephp + '"',
                callback: function (error) {
                    if (!error) {
                        grunt.log.writeln("Moodle theme cache reset.");
                    }
                }
            }
        },
        watch: {
            // Watch for any changes to less files and compile.
            files: ["scss/*.scss"],
            tasks: ["compile"],
            options: {
                spawn: false,
                livereload: true
            }
        }
    });

    // Register tasks.
    grunt.registerTask("default", ["watch"]);
    grunt.registerTask("decache", ["exec:decache"]);
    grunt.registerTask("compile", ["sass", "decache"]);
};
