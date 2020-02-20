module.exports = function(grunt) {

grunt.initConfig({
    stylelint: {
      css: ['**/*.css'],
      less: ['**/*.less']
    }
  });

  grunt.loadNpmTasks( 'grunt-stylelint' );

  grunt.registerTask('default', ['stylelint']);

};