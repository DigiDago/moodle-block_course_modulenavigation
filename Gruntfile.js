module.exports = function(grunt) {

    // Import modules.
    var path = require('path');

    // Theme Bootstrap constants.
    var LESSDIR         = 'less',
        THEMEDIR        = path.basename(path.resolve('.'));

    // PHP strings for exec task.
    var moodleroot = path.dirname(path.dirname(__dirname)),
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
        less: {
            // Compile moodle styles.
            moodle: {
                options: {
                    compress: false
                },
                src: 'less/course_modulenavigation.less',
                dest: 'styles.css'
            }
        },
        exec: {
            decache: {
                cmd: 'php -r "' + decachephp + '"',
                callback: function(error, stdout, stderror) {
                    if (!error) {
                        grunt.log.writeln("Moodle theme cache reset.");
                    }
                }
            }
        },
        watch: {
            // Watch for any changes to less files and compile.
            files: ["less/*.less"],
            tasks: ["compile"],
            options: {
                spawn: false,
                livereload: true
            }
        }
    });

    // Load contrib tasks.
    grunt.loadNpmTasks("grunt-contrib-less");
    grunt.loadNpmTasks("grunt-contrib-watch");
    grunt.loadNpmTasks("grunt-text-replace");
    grunt.loadNpmTasks("grunt-exec");

    // Register tasks.
    grunt.registerTask("default", ["watch"]);
    grunt.registerTask("decache", ["exec:decache"]);

    grunt.registerTask("compile", ["less", "decache"]);
};
