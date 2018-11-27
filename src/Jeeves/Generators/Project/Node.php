<?php

namespace Mygento\Jeeves\Generators\Project;

class Node
{
    public function generatePackages($name)
    {
        return json_encode(
            [
            'name' => $name,
            'private' => true,
            'version' => '1.0.0',
            'description' => '',
            'main' => 'gulpfile.js',
            'dependencies' => [
                'font-awesome' => '^4.7.0',
                'gulp' => '4.0.0',
                'gulp-changed' => '^3.2.0',
                'gulp-cssnano' => '^2.1.3',
                'gulp-eslint' => '^5.0.0',
                'gulp-sass' => '^4.0.2',
                'gulp-scss-lint' => '^0.7.1',
                'node-normalize-scss' => '^8.0.0',
                'node-reset-scss' => '^1.0.1',
                'sassime' => '^1.1.6',
            ],
            'devDependencies' => [
                'gulp-notify' => '^3.2.0',
                'gulp-sourcemaps' => '^2.6.4',
                'guppy-pre-commit' => '^0.4.0',
            ],
            ],
            JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES
        );
    }

    public function generateJsLint()
    {
        return json_encode(
            [
            'env' => [
                'node' => true,
                'browser' => true,
                'jquery' => true,
                'amd' => true,
                'prototypejs' => true,
            ],
            'globals' => new \ArrayObject(),
            'parserOptions' => [
                'ecmaVersion' => 6
            ],
            'extends' => 'eslint:recommended',
            'rules' => [
                'indent' => ['error', 2],
                'keyword-spacing' => [
                  'error',
                  [
                    'before' => true,
                    'after' => true,
                  ]
                ],
                'linebreak-style' => [
                  'error',
                  'unix',
                ],
                'no-multiple-empty-lines' => 'error',
                'no-unused-vars' => 'warn',
                'quotes' => [
                  'error',
                  'single',
                ],
                'semi' => [
                  'error',
                  'always',
                ],
                'space-before-blocks' => [
                  'error',
                  'always',
                ],
            ],
            ],
            JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES
        );
    }

    public function generateGulp($name)
    {
        $theme_folder = '${theme_folder}';
        $scss_folder = '${scss_folder}';
        $config = <<<CONFIG
const notProduction = process.env.NODE_ENV !== 'production';

const gulp = require('gulp');
const sass = require('gulp-sass');
const cssnano = require('gulp-cssnano');
const scsslint = require('gulp-scss-lint');
const eslint = require('gulp-eslint');
const noop = require('through2').obj();

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
    add: false,
    browsers: ['> 1%']
  })
};


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

gulp.task('lint', gulp.parallel('scss-lint', 'js-lint'));
gulp.task('pre-commit', gulp.series('lint'));

gulp.task('scss', () => {
  return gulp.src([`${scss_folder}/**/*.scss`])
    .pipe(notProduction ? sourcemaps.init() : noop())
    .pipe(sass(include_opt).on('error', sass.logError))
    .pipe(cssnano(css_options))
    .pipe(notProduction ? sourcemaps.write('.') : noop())
    .pipe(gulp.dest(css_folder))
    .pipe(notProduction ? notify({
      message: 'Styles complete',
      onLast: true
    }) : noop());
});

gulp.task('serve', gulp.series('scss', () => {
  return gulp.watch(`${scss_folder}/**/*.scss`, gulp.series('scss'));
}));

gulp.task('build', gulp.series('scss'));
gulp.task('default', gulp.parallel('serve', 'pre-commit'));

CONFIG;
        return $config;
    }
}
