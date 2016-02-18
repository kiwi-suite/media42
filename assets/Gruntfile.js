module.exports = function(grunt) {
    grunt.initConfig({
        vendor_dir: 'bower_components',
        dist: 'dist',

        bower: {
            install: {
                options: {
                    copy: false
                }
            }
        },

        concurrent: {
            all: ['compile-vendor-js', 'compile-app-js']
        },

        concat: {
            options: {
                separator: ';\n'
            },
            vendor: {
                src: [
                    '<%= vendor_dir %>/angular-file-upload/dist/angular-file-upload.min.js',
                    '<%= vendor_dir %>/cropper/dist/cropper.js',
                    '<%= vendor_dir %>/ng-cropper/dist/ngCropper.js'
                ],
                dest: '<%= dist %>/js/vendor.js'
            },
            app: {
                src: [
                    'javascripts/*.js',
                    'javascripts/directive/*.js',
                    'javascripts/filter/*.js',
                    'javascripts/controller/*.js',
                    'javascripts/service/*.js'
                ],
                dest: '<%= dist %>/js/media42.js'
            }
        },

        uglify: {
            options: {
                mangle: false
            },
            vendor: {
                src: '<%= dist %>/js/vendor.js',
                dest: '<%= dist %>/js/vendor.min.js'
            },
            app: {
                src: '<%= dist %>/js/media42.js',
                dest: '<%= dist %>/js/media42.min.js'
            }
        },

        clean: {
            all: ['<%= dist %>/js/'],

            vendorjs: [
                //'<%= dist %>/js/vendor.js'
            ],
            appjs: [
                //'<%= dist %>/js/admin42.js'
            ]
        },

        watch: {
            grunt: {
                files: ['Gruntfile.js', 'bower.json'],
                tasks: ['default']

            },
            js: {
                files: ['javascripts/**/*.js'],
                tasks: ['compile-app-js']
            }
        }
    });

    grunt.registerTask('default', ['bower', 'concurrent:all']);
    grunt.registerTask('compile-vendor-js', ['concat:vendor', 'uglify:vendor', 'clean:vendorjs']);
    grunt.registerTask('compile-app-js', ['concat:app', 'uglify:app', 'clean:appjs']);
    grunt.registerTask('clear', ['clean:all']);

    require('load-grunt-tasks')(grunt);
};
