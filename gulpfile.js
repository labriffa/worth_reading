var gulp = require('gulp')
    sass = require('gulp-sass')
    uglify = require('gulp-uglify');

const SASS_DIR = 'app/Resources/public/sass';
const JS_DIR   = 'app/Resources/public/js';

gulp.task('sass', function() {
    return gulp.src(SASS_DIR + '/*')
        .pipe(sass())
        .pipe(gulp.dest('web/css'));
});

gulp.task('js', function() {
    return gulp.src(JS_DIR + '/*')
        .pipe(uglify())
        .pipe(gulp.dest('web/js'));
});

gulp.task('watch', function() {
    gulp.watch(SASS_DIR + '/**/*.scss', ['sass']);
    gulp.watch(JS_DIR + '/**/*.js', ['js'])
});