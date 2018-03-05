const gulp = require('gulp');
const sourcemaps = require('gulp-sourcemaps');
const babel = require('gulp-babel');
const concat = require('gulp-concat');
const uglify = require('gulp-uglify');
const rename = require('gulp-rename');

gulp.task('build:designer:babel', () => {
    gulp.src([
        'src/js/util/**/*.js',
        'src/js/designer/**/*.js'
    ])
        .pipe(sourcemaps.init())
        .pipe(babel({
            presets: ['babel-preset-env']
        }))
        .pipe(concat('designer.js'))
        .pipe(sourcemaps.write())
        .pipe(gulp.dest('dist'))
        .pipe(sourcemaps.init())
        .pipe(uglify())
        .pipe(rename('designer.min.js'))
        .pipe(sourcemaps.write())
        .pipe(gulp.dest('dist'));
});

gulp.task('build:frontend:babel', () => {
    gulp.src([
        'src/js/util/**/*.js',
        'src/js/frontend/perfect-scrollbar.js',
        'src/js/frontend/scrollhelper.js'
    ])
        .pipe(sourcemaps.init())
        .pipe(babel({
            presets: ['babel-preset-env']
        }))
        .pipe(concat('frontend.js'))
        .pipe(sourcemaps.write())
        .pipe(gulp.dest('dist'))
        .pipe(sourcemaps.init())
        .pipe(uglify())
        .pipe(rename('frontend.min.js'))
        .pipe(sourcemaps.write())
        .pipe(gulp.dest('dist'));
});

gulp.task('build', ['build:designer:babel', "build:frontend:babel"]);

gulp.task('default', ['build']);