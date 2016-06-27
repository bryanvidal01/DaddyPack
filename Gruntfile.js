module.exports = function(grunt) {

    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),
        jshint: {
            // You get to make the name
            // The paths tell JSHint which files to validate
            all: [
                'wp-content/themes/clrz_theme_default/js/*.js',
                'wp-content/themes/clrz_theme_default/js/classes/*.js',
            ],
        },
        imagemin: {
            png: {
                options: {
                    optimizationLevel: 5
                },
                files: [{
                    // Set to true to enable the following options…
                    expand: true,
                    // cwd is 'current working directory'
                    cwd: 'wp-content/themes/',
                    src: ['**/*.png'],
                    // Could also match cwd line above. i.e. project-directory/img/
                    dest: 'wp-content/themes/',
                    ext: '.png'
                }]
            }
        }

    });

    grunt.loadNpmTasks('grunt-contrib-imagemin');
    grunt.loadNpmTasks('grunt-contrib-jshint');

    grunt.registerTask('default', ['imagemin']);

};