module.exports = function(grunt) {
    var package = grunt.file.readJSON('package.json');

    grunt.initConfig({
        makepot: {
            target: {
                options: {
                    exclude: ['dists/.*', 'node_modules/*', 'assets/*', 'tests/*', 'bin/*'],
                    mainFile: 'awesome-pm.php',
                    domainPath: '/languages/',
                    potFilename: 'awesome-pm.pot',
                    type: 'wp-plugin',
                    updateTimestamp: true,
                    potHeaders: {
                        'report-msgid-bugs-to': 'https://github.com/promyaaa/awesome-project-manager/issues',
                        'language-team': 'LANGUAGE <EMAIL@ADDRESS>',
                        poedit: true,
                        'x-poedit-keywordslist': true
                    }
                }
            }
        },

        clean: {
            main: ['dists/']
        },

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

        compress: {
            main: {
                options: {
                    mode: 'zip',
                    archive: './dists/awesome-pm-v' + package.version + '.zip'
                },
                expand: true,
                cwd: 'dists/',
                src: ['**/*'],
                dest: 'awesome-pm'
            }
        }
    });

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
