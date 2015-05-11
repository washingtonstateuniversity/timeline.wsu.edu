module.exports = function(grunt) {
	grunt.initConfig({
		pkg: grunt.file.readJSON('package.json'),

		uglify: {
			theme_scripts: {
				src: 'js/timeline.js',
				dest: 'js/timeline.min.js'
			}
		},

		watch: {
			files: [
				'js/timeline.js'
			],
			tasks: ['default']
		}
	});

	grunt.loadNpmTasks('grunt-contrib-uglify');
	grunt.loadNpmTasks('grunt-contrib-watch');

	// Default task(s).
	grunt.registerTask('default', ['uglify']);
};
