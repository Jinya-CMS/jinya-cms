const gulp = require('gulp');
const babel = require('gulp-babel');

gulp.task('default', () => gulp
  .src('js/scrollhelper.js')
  .pipe(babel({
    presets: ['@babel/env'],
  }))
  .pipe(gulp.dest('js/dist')));
