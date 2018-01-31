module.exports = function(grunt) {
    var package = grunt.file.readJSON('package.json');

    grunt.initConfig({

        // Generate POT files.
        makepot: {
            target: {
                options: {
                    exclude: ['dists/.*', 'node_modules/*', 'assets/*', 'tests/*', 'bin/*'],
                    mainFile: 'fusion-pm.php',
                    domainPath: '/languages/',
                    potFilename: 'fusion-pm.pot',
                    type: 'wp-plugin',
                    updateTimestamp: true,
                    potHeaders: {
                        'report-msgid-bugs-to': 'https://wptarzan.com/',
                        'language-team': 'LANGUAGE <EMAIL@ADDRESS>',
                        poedit: true,
                        'x-poedit-keywordslist': true
                    }
                }
            }
        },

        // Clean up build directory
        clean: {
            main: ['dists/']
        },

        // Copy the plugin into the build directory
        copy: {
            main: {
                src: [
                    '**',
                    '!.git/**',
                    '!dists/**',
                    '!node_modules/**',
                    '!bin/**',
                    '!.gitignore',
                    '!.gitmodules',
                    '!npm-debug.log',
                    '!.babelrc',
                    '!secret.json',
                    '!plugin-deploy.sh',
                    '!assets/src/**',
                    '!assets/css/style.css.map',
                    '!tests/**',
                    '!Gruntfile.js',
                    '!package.json',
                    '!debug.log',
                    '!webpack.config.js',
                    '!phpunit.xml',
                    '!**/Gruntfile.js',
                    '!**/package.json',
                    '!**/README.md',
                    '!**/*~'
                ],
                dest: 'dists/'
            }
        },

        //Compress build directory into <name>.zip and <name>-<version>.zip
        compress: {
            main: {
                options: {
                    mode: 'zip',
                    archive: './dists/fusion-pm-v' + package.version + '.zip'
                },
                expand: true,
                cwd: 'dists/',
                src: ['**/*'],
                dest: 'fusion-pm'
            }
        }
    });

    // Load NPM tasks to be used here
    grunt.loadNpmTasks( 'grunt-wp-i18n' );
    grunt.loadNpmTasks( 'grunt-contrib-clean' );
    grunt.loadNpmTasks( 'grunt-contrib-copy' );
    grunt.loadNpmTasks( 'grunt-contrib-compress' );

    grunt.registerTask( 'release', [
        'makepot'
    ]);

    grunt.registerTask( 'zip', [
        'clean',
        'copy',
        'compress'
    ]);
};
