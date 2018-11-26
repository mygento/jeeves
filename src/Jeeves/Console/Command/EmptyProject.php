<?php

namespace Mygento\Jeeves\Console\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class EmptyProject extends BaseCommand
{
    protected function configure()
    {
        $this
            ->setName('project-template')
            ->setAliases(['empty-project'])
            ->setDescription('Create new project template')
            ->setDefinition([
                new InputArgument('name', InputArgument::OPTIONAL, 'Name of the entity'),
                new InputArgument('repo', InputArgument::OPTIONAL, 'Project repository url'),
                new InputArgument('vendor', InputArgument::OPTIONAL, 'Vendor of the module', 'mygento'),
                new InputArgument('path', InputArgument::OPTIONAL, 'Path of the module', '.'),
            ])
            ->setHelp(
                <<<EOT
<info>php jeeves.phar project-template</info>
EOT
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = $this->getIO();
        $path = $input->getArgument('path') . '/';
        $vendor = strtolower($input->getArgument('vendor'));
        $name = strtolower($input->getArgument('name'));
        $name = $io->askAndValidate(
            'Project Name [<comment>' . $name . '</comment>]: ',
            function ($value) use ($name) {
                if (null === $value) {
                    return $name;
                }
                return $value;
            },
            null,
            $name
        );

        $repo = strtolower($input->getArgument('repo'));
        $repo = $io->askAndValidate(
            'Project repository [<comment>' . $repo . '</comment>]: ',
            function ($value) use ($repo) {
                if (null === $value) {
                    return $repo;
                }
                return $value;
            },
            null,
            $repo
        );

        $composer = [
            'name' => $vendor . '/' . $name,
            'type' => 'project',
            'version' => '2.2.6',
            'require' => [
                'magento/product-community-edition' => '~2.2',
                'composer/composer' => '1.4.1',
                'mygento/base' => '~2.2',
                'mygento/module-configsync' => '~2.2',
                'mageplaza/module-smtp' => '^1.1.0',
                'etws/magento-language-ru_ru' => 'dev-develop',
            ],
            'require-dev' => [
                'mygento/coding-standard' => '~2.2'
            ],
            'replace' => [
              'magento/module-marketplace' => '*',
              'magento/module-cybersource' => '*',
              'magento/module-authorizenet' => '*',
              'magento/module-signifyd' => '*',
              'amzn/amazon-pay-module' => '*',
              'amzn/amazon-pay-and-login-magento-2-module' => '*',
              'amzn/amazon-pay-and-login-with-amazon-core-module' => '*',
              'amzn/login-with-amazon-module' => '*',
              'dotmailer/dotmailer-magento2-extension' => '*',
              'klarna/module-core' => '*',
              'klarna/module-kp' => '*',
              'klarna/module-ordermanagement' => '*',
              'shopialfb/facebook-module' => '*',
              'temando/module-shipping-m2' => '*',
              'vertex/module-tax' => '*',
            ],
            'autoload' => [
                    'psr-4' => [
                        'Magento\\Framework\\' => 'lib/internal/Magento/Framework/',
                        'Magento\\Setup\\' => 'setup/src/Magento/Setup/',
                        'Magento\\' => 'app/code/Magento/',
                    ],
                    'psr-0' => [
                        '' => ['app/code/'],
                    ],
                    'files' => ['app/etc/NonComposerComponentRegistration.php'],
                    'exclude-from-classmap' => [
                        '**/dev/**',
                        '**/update/**',
                        '**/Test/**'
                    ]
            ],
            'autoload-dev' => [
                'psr-4' => [
                    'Magento\\Sniffs\\' => 'dev/tests/static/framework/Magento/Sniffs/',
                    'Magento\\Tools\\' => 'dev/tools/Magento/Tools/',
                    'Magento\\Tools\\Sanity\\' => 'dev/build/publication/sanity/Magento/Tools/Sanity/',
                    'Magento\\TestFramework\\Inspection\\' => 'dev/tests/static/framework/Magento/TestFramework/Inspection/',
                    'Magento\\TestFramework\\Utility\\' =>'dev/tests/static/framework/Magento/TestFramework/Utility/'
                ],
            ],
            'minimum-stability' => 'stable',
            'prefer-stable' => true,
            'repositories' => [
                [
                    'type' => 'composer',
                    'url' => 'https://repo.magento.com/',
                ],
            ],
            'extra' => [
                'magento-force' => 'override',
            ],
        ];

        $this->writeFile($path . 'composer.json', json_encode($composer, JSON_PRETTY_PRINT |  JSON_UNESCAPED_SLASHES));

        $packages = [
            'name' => $name,
            'license' => 'private',
            'version' => '1.0.0',
            'description' => '',
            'main' => 'Gulpfile.js',
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
        ];

        $this->writeFile($path . 'package.json', json_encode($packages, JSON_PRETTY_PRINT |  JSON_UNESCAPED_SLASHES));

        $IS_PULL_REQUEST = '$IS_PULL_REQUEST';
        $BRANCH = '$BRANCH';

        $shippable = <<<CONFIG
branches:
  only:
    - stage
    - production

build:
  pre_ci_boot:
    image_name: mygento/deployer
    image_tag: v1-full
    pull: true
  ci:
    - apt-get install libxml2-utils
    - composer self-update
    - bundle install
    - if [[ $IS_PULL_REQUEST == true ]]; then npm install --production --silent --no-progress; fi
    - if [[ $IS_PULL_REQUEST == true ]]; then NODE_ENV=production gulp lint; fi
    - if [[ $IS_PULL_REQUEST == true ]]; then rm composer.json; rm composer.lock; fi
    - if [[ $IS_PULL_REQUEST == true ]]; then composer require mygento/coding-standard --quiet; fi
    - if [[ $IS_PULL_REQUEST == true ]]; then php vendor/bin/grumphp run; fi
  on_success:
    - if [[ $IS_PULL_REQUEST != true ]]; then mina $BRANCH deploy; fi

CONFIG;

        $this->writeFile($path . '.shippable.yml', $shippable);
        $d = 'd' . 'ie';
        $v = 'var' . '_dump';
        $e = 'e' . 'xit';
        $grum = <<<CONFIG
parameters:
  git_dir: .
  bin_dir: vendor/bin
  tasks:
    git_blacklist:
      keywords:
        - "$d("
        - "$v("
        - "$e;"
    phplint:
    phpcsfixer2:
      config: '.php_cs'
    xmllint:

CONFIG;

        $this->writeFile($path . 'grumphp.yml', $grum);

        $mina = <<<RUBY
require 'mina/deploy'
require 'mina/git'

set :application_name, '$name' # application  name
set :repository, '$repo'

set :shared_dirs, ['var/log','var/report','var/mygento/cml','pub/media', 'var/import','var/export']
set :shared_files, ['app/etc/env.php','pub/sitemap.xml']

# default
set :ssh_options, '-A'
set :forward_agent, true

task :stage do
  set :user, '$name'
  set :domain, 'host.ru'
  set :deploy_to, "/var/www/$name/host.ru"
  set :branch, 'stage'
  set :php_bin, 'php'
  set :composer_install_command, 'install --no-dev'
  set :keep_releases, 3
end

task :production do
  set :user, '$name'
  set :domain, 'host.ru'
  set :deploy_to, "/var/www/$name/host.ru"
  set :branch, 'production'
  set :php_bin, 'php'
  set :composer_install_command, 'install --no-dev --prefer-dist --no-interaction --quiet'
end

task :setup do
  command %{mkdir -p "#{fetch(:shared_path)}/pub/media"}
  command %{mkdir -p "#{fetch(:shared_path)}/var/log"}
  command %{mkdir -p "#{fetch(:shared_path)}/var/report"}
  command %{mkdir -p "#{fetch(:shared_path)}/var/mygento/cml"}
  command %{mkdir -p "#{fetch(:shared_path)}/app/etc"}
  command %[curl -s https://getcomposer.org/installer | php -- --install-dir=#{fetch(:shared_path)}]
end

task :composer do
  command "php #{fetch(:shared_path)}/composer.phar self-update"
  if ENV['MAGE_LOGIN']
    command "php #{fetch(:shared_path)}/composer.phar config http-basic.repo.magento.com #{ENV['MAGE_LOGIN']} #{ENV['MAGE_PWD']}"
  end
  command "php #{fetch(:shared_path)}/composer.phar #{fetch(:composer_install_command)}"
end

task :npm do
  command "npm install --production --silent --no-progress"
  command "NODE_ENV=production gulp build"
  command "rm -fR node_modules"
end

task :magento do
  command "PTH=$(pwd)"
  command "#{fetch(:php_bin)} \"\$PTH/bin/magento\" deploy:mode:set production -s"
  command "#{fetch(:php_bin)} \"\$PTH/bin/magento\" config:set dev/js/minify_files 1"
  command "#{fetch(:php_bin)} \"\$PTH/bin/magento\" config:set dev/css/minify_files 1"
  command "#{fetch(:php_bin)} \"\$PTH/bin/magento\" setup:upgrade --keep-generated"
  command "#{fetch(:php_bin)} \"\$PTH/bin/magento\" setup:di:compile -q"
  command "#{fetch(:php_bin)} \"\$PTH/bin/magento\" setup:static-content:deploy ru_RU -q"
end

desc "Deploys the current version to the server."
task :deploy do
  deploy do
    invoke :'git:clone'
    invoke :'composer'
    invoke :'npm'
    invoke :'deploy:link_shared_paths'
    invoke :'magento'

    on :launch do
        in_path(fetch(:current_path)) do
          command "#{fetch(:php_bin)} #{fetch(:current_path)}/bin/magento setup:config:sync #{fetch(:branch)} #{fetch(:current_path)}/config/config.yml"
          command "#{fetch(:php_bin)} #{fetch(:current_path)}/bin/magento cache:flush"
          invoke :'deploy:cleanup'
        end
    end
  end
end
RUBY;
        $editor = <<<EDITOR
root = true

[*]
indent_style = space
indent_size = 4
end_of_line = lf
charset = utf-8
trim_trailing_whitespace = true

[*.{json,yml,js,scss,css}]
indent_size = 2

[*.md]
trim_trailing_whitespace = false

EDITOR;

        $this->writeFile($path . '.editorconfig', $editor);

        $gem = <<<GEM
source 'https://rubygems.org'

gem 'mina', git: 'https://github.com/luckyraul/mina.git', branch: 'relative_path'
gem 'scss_lint', require: false

GEM;

        $this->writeFile($path . 'Gemfile', $gem);

        if (!is_dir($path . 'config')) {
            mkdir($path . 'config');
        }
        $this->writeFile($path . 'config/deploy.rb', $mina);

        if (!is_dir($path . 'app')) {
            mkdir($path . 'app');
        }

        if (!is_dir($path . 'app/etc')) {
            mkdir($path . 'app/etc');
        }

        $config = <<<CONFIG
<?php
return array (
  'modules' =>
  array (
    'Magento_Store' => 1,
    'Magento_Directory' => 1,
    'Magento_AdvancedPricingImportExport' => 1,
    'Magento_Config' => 1,
    'Magento_Backend' => 1,
    'Magento_Theme' => 1,
    'Magento_Eav' => 1,
    'Magento_Backup' => 1,
    'Magento_Customer' => 1,
    'Magento_AdminNotification' => 1,
    'Magento_BundleImportExport' => 1,
    'Magento_CacheInvalidate' => 1,
    'Magento_Indexer' => 1,
    'Magento_Cms' => 1,
    'Magento_Security' => 1,
    'Magento_CatalogImportExport' => 1,
    'Magento_Rule' => 1,
    'Magento_Cron' => 1,
    'Magento_Catalog' => 1,
    'Magento_Search' => 1,
    'Magento_CatalogUrlRewrite' => 1,
    'Magento_Widget' => 1,
    'Magento_Quote' => 1,
    'Magento_SalesSequence' => 1,
    'Magento_Payment' => 1,
    'Magento_CmsUrlRewrite' => 1,
    'Magento_User' => 1,
    'Magento_ConfigurableImportExport' => 1,
    'Magento_Msrp' => 1,
    'Magento_CatalogInventory' => 1,
    'Magento_Contact' => 1,
    'Magento_Cookie' => 1,
    'Magento_Newsletter' => 1,
    'Magento_CurrencySymbol' => 1,
    'Magento_Sales' => 1,
    'Magento_Integration' => 1,
    'Magento_CustomerImportExport' => 1,
    'Magento_Deploy' => 1,
    'Magento_Developer' => 1,
    'Magento_Dhl' => 1,
    'Magento_Authorization' => 1,
    'Magento_Downloadable' => 1,
    'Magento_ImportExport' => 1,
    'Magento_Bundle' => 1,
    'Magento_Email' => 1,
    'Magento_EncryptionKey' => 1,
    'Magento_Fedex' => 1,
    'Magento_GiftMessage' => 0,
    'Magento_Checkout' => 1,
    'Magento_GoogleAnalytics' => 1,
    'Magento_Ui' => 1,
    'Magento_GroupedImportExport' => 1,
    'Magento_GroupedProduct' => 1,
    'Magento_DownloadableImportExport' => 1,
    'Magento_CatalogRule' => 1,
    'Magento_InstantPurchase' => 1,
    'Magento_Analytics' => 1,
    'Magento_LayeredNavigation' => 1,
    'Magento_Marketplace' => 1,
    'Magento_MediaStorage' => 1,
    'Magento_ConfigurableProduct' => 1,
    'Magento_Multishipping' => 1,
    'Magento_NewRelicReporting' => 1,
    'Magento_Reports' => 1,
    'Magento_OfflinePayments' => 1,
    'Magento_SalesRule' => 1,
    'Magento_PageCache' => 1,
    'Magento_Vault' => 1,
    'Magento_Paypal' => 1,
    'Magento_Persistent' => 1,
    'Magento_ProductAlert' => 1,
    'Magento_ProductVideo' => 1,
    'Magento_CheckoutAgreements' => 1,
    'Magento_QuoteAnalytics' => 1,
    'Magento_ReleaseNotification' => 1,
    'Magento_Review' => 1,
    'Magento_RequireJs' => 1,
    'Magento_Shipping' => 1,
    'Magento_ReviewAnalytics' => 1,
    'Magento_Robots' => 1,
    'Magento_Rss' => 1,
    'Magento_CatalogRuleConfigurable' => 1,
    'Magento_Captcha' => 1,
    'Magento_SalesAnalytics' => 1,
    'Magento_SalesInventory' => 1,
    'Magento_OfflineShipping' => 1,
    'Magento_ConfigurableProductSales' => 1,
    'Magento_UrlRewrite' => 1,
    'Magento_CatalogSearch' => 1,
    'Magento_CustomerAnalytics' => 1,
    'Magento_SendFriend' => 1,
    'Magento_Wishlist' => 1,
    'Magento_Signifyd' => 1,
    'Magento_Sitemap' => 1,
    'Magento_Authorizenet' => 1,
    'Magento_Swagger' => 1,
    'Magento_Swatches' => 1,
    'Magento_SwatchesLayeredNavigation' => 1,
    'Magento_Tax' => 1,
    'Magento_TaxImportExport' => 1,
    'Magento_GoogleAdwords' => 1,
    'Magento_Translation' => 1,
    'Magento_GoogleOptimizer' => 1,
    'Magento_Ups' => 1,
    'Magento_SampleData' => 1,
    'Magento_CatalogAnalytics' => 1,
    'Magento_Usps' => 1,
    'Magento_Variable' => 1,
    'Magento_Braintree' => 1,
    'Magento_Version' => 1,
    'Magento_Webapi' => 1,
    'Magento_WebapiSecurity' => 1,
    'Magento_Weee' => 1,
    'Magento_CatalogWidget' => 1,
    'Magento_WishlistAnalytics' => 1,
    'Mygento_Base' => 1,
    'Mygento_Configsync' => 1
  ),
);
CONFIG;
        $this->writeFile($path . 'app/etc/config.php', $config);

        $finder = '$finder';
        $config = <<<CONFIG
<?php
$finder = PhpCsFixer\Finder::create()
    ->in('app')
    ->name('*.phtml')
    ->exclude('dev/tests/functional/generated')
    ->exclude('dev/tests/functional/var')
    ->exclude('dev/tests/functional/vendor')
    ->exclude('dev/tests/integration/tmp')
    ->exclude('dev/tests/integration/var')
    ->exclude('lib/internal/Cm')
    ->exclude('lib/internal/Credis')
    ->exclude('lib/internal/Less')
    ->exclude('lib/internal/LinLibertineFont')
    ->exclude('pub/media')
    ->exclude('pub/static')
    ->exclude('setup/vendor')
    ->exclude('var');

return PhpCsFixer\Config::create()
    ->setFinder($finder)
    ->setRules([
        '@PSR2' => true,
        'array_syntax' => ['syntax' => 'short'],
        'concat_space' => ['spacing' => 'one'],
        'include' => true,
        'new_with_braces' => true,
        'no_empty_statement' => true,
        'no_extra_consecutive_blank_lines' => true,
        'no_leading_import_slash' => true,
        'no_leading_namespace_whitespace' => true,
        'no_multiline_whitespace_around_double_arrow' => true,
        'no_multiline_whitespace_before_semicolons' => true,
        'no_singleline_whitespace_before_semicolons' => true,
        'no_trailing_comma_in_singleline_array' => true,
        'no_unused_imports' => true,
        'no_whitespace_in_blank_line' => true,
        'object_operator_without_whitespace' => true,
        'ordered_imports' => true,
        'standardize_not_equals' => true,
        'ternary_operator_spaces' => true,
        // mygento
        'phpdoc_order' => true,
        'phpdoc_types' => true,
        'phpdoc_add_missing_param_annotation' => true,
        'single_quote' => true,
        'standardize_not_equals' => true,
        'ternary_to_null_coalescing' => true,
        'ternary_operator_spaces' => true,
        'lowercase_cast' => true,
        'no_empty_comment' => true,
        'no_empty_phpdoc' => true,
    ]);

CONFIG;

        $this->writeFile($path . '.php_cs', $config);

        $scss = <<<CONFIG
severity: error

linters:

  BangFormat:
    enabled: true
    space_before_bang: true
    space_after_bang: false

  BemDepth:
    enabled: true
    max_elements: 3

  BorderZero:
    enabled: true
    convention: zero

  ChainedClasses:
    enabled: false

  ColorKeyword:
    enabled: true

  ColorVariable:
    enabled: true

  Comment:
    enabled: false

  DebugStatement:
    enabled: true

  DeclarationOrder:
    enabled: true

  DisableLinterReason:
    enabled: true

  DuplicateProperty:
    enabled: true

  ElsePlacement:
    enabled: true
    style: same_line

  EmptyLineBetweenBlocks:
    enabled: true
    ignore_single_line_blocks: true

  EmptyRule:
    enabled: true

  ExtendDirective:
    enabled: false

  FinalNewline:
    enabled: true
    present: true

  HexLength:
    enabled: true
    style: short

  HexNotation:
    enabled: true
    style: lowercase

  HexValidation:
    enabled: true

  IdSelector:
    enabled: true
    severity: warning

  ImportantRule:
    enabled: true
    severity: warning

  ImportPath:
    enabled: true
    leading_underscore: false
    filename_extension: false

  Indentation:
    enabled: true
    allow_non_nested_indentation: true
    character: space
    width: 2

  LeadingZero:
      enabled: true
      style: include_zero

  LengthVariable:
    enabled: false
    severity: warning

  MergeableSelector:
    enabled: false
    force_nesting: false

  NameFormat:
    enabled: true
    convention: hyphenated_lowercase
    allow_leading_underscore: true

  NestingDepth:
    enabled: true
    max_depth: 3

  PlaceholderInExtend:
    enabled: true

  PrivateNamingConvention:
    enabled: true
    prefix: _

  PropertyCount:
    enabled: false

  PropertySortOrder:
    enabled: false

  PropertySpelling:
    enabled: true
    extra_properties: []

  PropertyUnits:
    enabled: true
    global: ['em', 'rem', '%', 'vh', 'vw']
    properties:
      line-height: [] # No units allowed
      font-size: [] # No units allowed
      border: []
      transition: ['s']

  PseudoElement:
    enabled: true

  QualifyingElement:
    enabled: true
    allow_element_with_attribute: false
    allow_element_with_class: false
    allow_element_with_id: false
    severity: warning

  SelectorDepth:
    enabled: true
    max_depth: 3

  SelectorFormat:
    enabled: false
    convention: hyphenated_lowercase

  Shorthand:
    enabled: true

  SingleLinePerProperty:
    enabled: true
    allow_single_line_rule_sets: false

  SingleLinePerSelector:
    enabled: true

  SpaceAfterComma:
    enabled: true

  SpaceAfterPropertyColon:
    enabled: true
    style: one_space

  SpaceAfterPropertyName:
    enabled: true

  SpaceAfterVariableColon:
    enabled: true
    style: at_least_one_space

  SpaceAfterVariableName:
    enabled: true

  SpaceAroundOperator:
    enabled: true
    style: one_space

  SpaceBeforeBrace:
    enabled: true
    style: space
    allow_single_line_padding: true

  SpaceBetweenParens:
    enabled: true
    spaces: 0

  StringQuotes:
    enabled: true
    style: single_quotes

  TrailingSemicolon:
    enabled: true

  TrailingZero:
    enabled: true

  TrailingWhitespace:
    enabled: true

  TransitionAll:
    enabled: false

  UnnecessaryMantissa:
    enabled: true

  UnnecessaryParentReference:
    enabled: true

  UrlFormat:
    enabled: false

  UrlQuotes:
    enabled: true

  VariableForProperty:
    enabled: true
    properties:
      - color
      - font-size
      - line-height

  VendorPrefixes:
    enabled: true
    identifier_list: base
    include: []
    exclude: []

  ZeroUnit:
    enabled: true

CONFIG;

        $this->writeFile($path . '.scss-lint.yml', $scss);

        $eslint = [
          'env' => [
            'browser' => true,
            'jquery' => true,
            'amd' => true,
            'prototypejs' => true,
          ],
          'globals' => new \ArrayObject(),
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
        ];

        $this->writeFile($path . '.eslintrc.json', json_encode($eslint, JSON_PRETTY_PRINT |  JSON_UNESCAPED_SLASHES));
        $theme_folder = '${theme_folder}';
        $scss_folder = '${scss_folder}';
        $gulp = <<<CONFIG
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

const theme_folder = './app/design/frontend/Mygento/$name';
const scss_folder = `$theme_folder/web/scss`;
const css_folder = `$theme_folder/web/css`;

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
    return gulp.src([`$scss_folder/**/*.scss`, `!$scss_folder/vendor/**/*.scss`])
        .pipe(scsslint({
            'maxBuffer': 307200
        }))
        .pipe(scsslint.failReporter('E'));
});
gulp.task('js-lint', () => {
    return gulp.src([`$theme_folder/**/*.js`, '!node_modules/**', `!$theme_folder/web/js/vendor/**/*.js`, `!$theme_folder/web/mage/**/*.js`])
      .pipe(eslint())
      .pipe(eslint.format())
      .pipe(eslint.failAfterError());
});

gulp.task('lint', ['scss-lint', 'js-lint']);

gulp.task('build', ['scss']);
gulp.task('default', ['serve', 'pre-commit']);

gulp.task('scss', () => {
    return gulp.src([`$scss_folder/**/*.scss`])
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
    gulp.watch(`$scss_folder/**/*.scss`, ['scss']);
});

CONFIG;

        $this->writeFile($path . 'Gulpfile.js', $gulp);
    }
}
