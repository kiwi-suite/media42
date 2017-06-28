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
            all: ['compile-vendor-js', 'compile-app-js', 'less:app']
        },

        concat: {
            options: {
                separator: ';\n'
            },
            vendor: {
                src: [
                    '<%= vendor_dir %>/angular-file-upload/dist/angular-file-upload.min.js',
                    '<%= vendor_dir %>/cropper/dist/cropper.js',
                ],
                dest: '<%= dist %>/js/vendor.js'
            },
            app: {
                src: [
                    'javascripts/*.js',
                    'javascripts/directive/*.js',
                    'javascripts/directive/form/*.js',
                    'javascripts/filter/*.js',
                    'javascripts/controller/*.js',
                    'javascripts/controller/link/*.js',
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

        less: {
            options: {
                compress: true,
                cleancss: true
            },
            app: {
                files: {
                    '<%= dist %>/css/media42.min.css': [
                        '<%= vendor_dir %>/cropper/dist/cropper.css'
                    ]
                }
            }
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
    grunt.registerTask('compile-css', ['less:app']);
    grunt.registerTask('clear', ['clean:all']);

    require('load-grunt-tasks')(grunt);
};
