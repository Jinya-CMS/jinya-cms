const gulp = require('gulp');
const babel = require('gulp-babel');
const rename = require('gulp-rename');

gulp.task('default', () => gulp
  .src([
    'scripts/*.js',
    '!scripts/*.dist.js',
  ])
  .pipe(babel({
    presets: ['@babel/env'],
  }))
  .pipe(rename((path) => {
    // eslint-disable-next-line
    path.basename += '.dist';
  }))
  .pipe(gulp.dest('scripts')));
