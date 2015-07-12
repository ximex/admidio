var gulp = require('gulp');
var less = require('gulp-less');
var autoprefixer = require('gulp-autoprefixer');
var minify = require('gulp-minify-css');
var rename = require('gulp-rename');
var imagemin = require('gulp-imagemin');


var paths = {
    root: './',
    libsExclude: '!adm_program/libs/**/*',
    theme: 'adm_themes/modern/',
    images: ['**/*.gif', '**/*.jpg', '**/*.jpeg', '**/*.png', '**/*.svg', '!adm_program/libs/**/*']
};


gulp.task('css', function () {
    gulp.src(paths.theme + 'css/*.less')
        .pipe(less())
        .pipe(autoprefixer())
        //.pipe(rename({
        //    extname: '.css'
        //}))
        //.pipe(gulp.dest(paths.theme + 'css/'))
        .pipe(minify())
        .pipe(rename({
            extname: '.min.css'
        }))
        .pipe(gulp.dest(paths.theme + 'css/'));
});

gulp.task('image', function () {
    gulp.src(paths.images)
        .pipe(imagemin({
            //optimizationLevel: 7,
            //multipass: true,
            //progressive: true,
            //interlaced: true
        }))
        .pipe(gulp.dest(paths.root));
});
