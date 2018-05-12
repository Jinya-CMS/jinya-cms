const gulp = require('gulp');

gulp.task('default', () => {
  gulp.src('js/scrollhelper.js')
    .pipe(gulp.dest('../public/dist'));
});