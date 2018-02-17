const gulp = require('gulp');
const sourcemaps = require('gulp-sourcemaps');
const babel = require('gulp-babel');
const concat = require('gulp-concat');
const uglify = require('gulp-uglify');
const rename = require('gulp-rename');

gulp.task('build:designer:babel', () => {
    gulp.src([
        'js/util/**/*.js',
        'js/designer/**/*.js'
    ])
        .pipe(sourcemaps.init())
        .pipe(babel({
            presets: ['babel-preset-env']
        }))
        .pipe(concat('designer.js'))
        .pipe(sourcemaps.write('.'))
        .pipe(gulp.dest('js'));
});

gulp.task('build:frontend:babel', () => {
    gulp.src([
        'js/util/**/*.js',
        'js/frontend/perfect-scrollbar.js',
        'js/frontend/scrollhelper.js'
    ])
        .pipe(sourcemaps.init())
        .pipe(babel({
            presets: ['babel-preset-env']
        }))
        .pipe(concat('frontend.js'))
        .pipe(sourcemaps.write('.'))
        .pipe(gulp.dest('js'));
});

gulp.task('build:frontend:uglify', ['build:frontend:babel'], () => {
    gulp.src('js/frontend.js')
        .pipe(uglify())
        .pipe(rename('frontend.min.js'))
        .pipe(gulp.dest('js'));
});

gulp.task('build:designer:uglify', ['build:designer:babel'], () => {
    gulp.src('js/designer.js')
        .pipe(uglify())
        .pipe(rename('designer.min.js'))
        .pipe(gulp.dest('js'));
});

gulp.task('build', ['build:designer:uglify', "build:frontend:uglify"]);

gulp.task('default', ['build']);