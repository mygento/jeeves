const notProduction = process.env.NODE_ENV !== 'production';

const gulp = require('gulp');
const path = require('path');
const util = require('gulp-util');
const sass = require('gulp-sass');
const cssnano = require('gulp-cssnano');
const scsslint = require('gulp-scss-lint');
const eslint = require('gulp-eslint');

if (notProduction) {
    var sourcemaps = require('gulp-sourcemaps');
    var notify = require('gulp-notify');
}

const theme_folder = './app/design/frontend/Mygento/sample';
const scss_folder = `${theme_folder}/web/scss`;
const css_folder = `${theme_folder}/web/css`;

const include_opt = {includePaths: [
  require('node-normalize-scss').includePaths,
  require('node-reset-scss').includePath,
  require('sassime').includePaths
]};

// CSS Config
const css_options = {
    zindex: false,
    autoprefixer: ({
        add: true,
        browsers: ['> 1%']
    })
};

gulp.task('pre-commit', ['lint']);

gulp.task('scss-lint', () => {
    return gulp.src([`${scss_folder}/**/*.scss`, `!${scss_folder}/vendor/**/*.scss`])
        .pipe(scsslint({
            'maxBuffer': 307200
        }))
        .pipe(scsslint.failReporter('E'));
});
gulp.task('js-lint', () => {
    return gulp.src([`${theme_folder}/**/*.js`, '!node_modules/**', `!${theme_folder}/web/js/vendor/**/*.js`, `!${theme_folder}/web/mage/**/*.js`])
      .pipe(eslint())
      .pipe(eslint.format())
      .pipe(eslint.failAfterError());
});

gulp.task('lint', ['scss-lint', 'js-lint']);

gulp.task('build', ['scss']);
gulp.task('default', ['serve', 'pre-commit']);

gulp.task('scss', () => {
    return gulp.src([`${scss_folder}/**/*.scss`])
        .pipe(notProduction ? sourcemaps.init() : util.noop())
        .pipe(sass(include_opt).on('error', sass.logError))
        .pipe(cssnano(css_options))
        .pipe(notProduction ? sourcemaps.write('.') : util.noop())
        .pipe(gulp.dest(css_folder))
        .pipe(notProduction ? notify({
            message: 'Styles complete',
            onLast: true
        }) : util.noop())
});

gulp.task('serve', ['scss'], () => {
    gulp.watch(`${scss_folder}/**/*.scss`, ['scss']);
});
