'use strict';

var gulp = require('gulp');
var karma = require('karma');
var path = require('path');

/**
 * Watch task
 */
gulp.task('watch', function () {
  gulp.watch(['ang/**/*.js', '!ang/test/karma.conf.js'], ['test']);
});

/**
 * Runs the unit tests
 */
gulp.task('test', function (done) {
  new karma.Server({
    configFile: path.resolve(__dirname, 'ang/test/karma.conf.js'),
    singleRun: true
  }, done).start();
});
