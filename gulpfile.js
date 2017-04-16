var gulp = require('gulp');
var concat = require('gulp-concat');
var rename = require('gulp-rename');
var uglify = require('gulp-uglify');

var files = [
	'node_modules/socket.io-client/dist/socket.io.js',
	'js/tattler.js'
];

var build = function () {
	gulp.src(files)
		.pipe(concat('tattler.min.js'))
		.pipe(uglify())
		.pipe(gulp.dest('js'));
};


gulp.task('build', function () {
	build();
});