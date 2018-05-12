const gulp = require('gulp');

gulp.task('default', () => {
  return gulp.src('js/scrollhelper.js')
    .pipe(gulp.dest('../public/dist'));
});