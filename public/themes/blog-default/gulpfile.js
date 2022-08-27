//npm run gulp watch

const gulp = require('gulp');
const fileinclude = require('gulp-file-include');
const sass = require('gulp-sass');
const formatHtml = require('gulp-format-html');
const through2 = require( 'through2' );    
const puppeteer = require('puppeteer');


const touch = () => through2.obj( function( file, enc, cb ) {
    if ( file.stat ) {
        file.stat.atime = file.stat.mtime = file.stat.ctime = new Date();
    }
    cb( null, file );
});

gulp.task('fileinclude', function() {
  return gulp.src(['./src/*.html', './src/**/*.html', '!**/_*/**'])
    .pipe(fileinclude({
      prefix: '@@',
      basepath: '@file'
    }))
    .pipe(formatHtml())
    .pipe( touch() )
    .pipe(gulp.dest('./'));
});

gulp.task('sass', function() {
  return gulp.src(['./scss/*.scss'])
    .pipe(sass())
    .pipe(gulp.dest('./css'));
});


gulp.task('watch', function () {
    gulp.watch(['./src/*.html', './src/**/*.html'], gulp.series('fileinclude'));
    gulp.watch(['./scss/*.scss'], gulp.series('sass'));
});


gulp.task('screenshots', function() {

	(async () => {
	  const browser = await puppeteer.launch();
	  const page = await browser.newPage();
	  //await page.setRequestInterception(true);
	  page.on('request', (request) => {
		if (request.resourceType() === 'image') request.abort();
		else request.continue();
	  });
	  await page.goto('https://www.vvveb.com');
	  await page.screenshot({ path: 'news.png', fullPage: true });

	  await browser.close();
	})();

});

// Default Task
gulp.task('default', gulp.series('fileinclude', 'sass'));
