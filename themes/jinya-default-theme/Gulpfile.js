const gulp = require('gulp');
const babel = require('gulp-babel');
const rename = require('gulp-rename');

gulp.task('default', () => gulp
  .src('scripts/scrollhelper.js')
  .pipe(babel({
    presets: ['@babel/env'],
  }))
  .pipe(rename('scrollhelper.dist.js'))
  .pipe(gulp.dest('scripts')));
