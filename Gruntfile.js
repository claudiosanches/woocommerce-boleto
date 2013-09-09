module.exports = function(grunt) {
"use strict";

    grunt.initConfig({

        // gets the package vars
        pkg: grunt.file.readJSON("package.json"),
        svn_settings: {
            path: "../../../../wp_plugins/<%= pkg.name %>",
            tag: "<%= svn_settings.path %>/tags/<%= pkg.version %>",
            trunk: "<%= svn_settings.path %>/trunk",
            exclude: [
                ".editorconfig",
                ".git/",
                ".gitignore",
                "node_modules/",
                "Gruntfile.js",
                "README.md",
                "package.json",
                "*.zip"
            ]
        },

        // image optimization
        imagemin: {
            dist: {
                options: {
                    optimizationLevel: 7,
                    progressive: true
                },
                files: [{
                    expand: true,
                    cwd: "./",
                    src: ["screenshot-*.png"],
                    dest: "./"
                }]
            }
        },

        // rsync commands used to take the files to svn repository
        rsync: {
            options: {
                args: ["--verbose"],
                exclude: "<%= svn_settings.exclude %>",
                recursive: true
            },
            tag: {
                options: {
                    src: "./",
                    dest: "<%= svn_settings.tag %>"
                }
            },
            trunk: {
                options: {
                src: "./",
                dest: "<%= svn_settings.trunk %>"
                }
            }
        },

        // shell command to commit the new version of the plugin
        shell: {
            svn_add: {
                command: 'svn add --force * --auto-props --parents --depth infinity -q',
                options: {
                    stdout: true,
                    stderr: true,
                    execOptions: {
                        cwd: "<%= svn_settings.path %>"
                    }
                }
            },
            svn_commit: {
                command: "svn commit -m 'updated the plugin version to <%= pkg.version %>'",
                options: {
                    stdout: true,
                    stderr: true,
                    execOptions: {
                        cwd: "<%= svn_settings.path %>"
                    }
                }
            }
        }
    });

    // load tasks
    grunt.loadNpmTasks('grunt-contrib-imagemin');
    grunt.loadNpmTasks("grunt-rsync");
    grunt.loadNpmTasks("grunt-shell");

    // default task
    grunt.registerTask("default", [
        "rsync:tag",
        "rsync:trunk",
        "shell:svn_add",
        "shell:svn_commit"
    ]);
};
