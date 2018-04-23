<?php

namespace Mygento\Jeeves\Console\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Symfony\Component\Yaml\Yaml;

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
            'version' => '2.2.0',
            'require' => [
                'magento/product-community-edition' => '~2.2',
                'composer/composer' => '1.4.1',
                'mygento/module-configsync' => '~2.2',
                'mageplaza/module-smtp' => '^1.1.0',
                'etws/magento-language-ru_ru' => 'dev-develop',
            ],
            'require-dev' => [
                'phpunit/phpunit' => '~6.2.0',
                'squizlabs/php_codesniffer' => '3.0.1',
                'phpmd/phpmd' => '@stable',
                'pdepend/pdepend' => '2.5.0',
                'friendsofphp/php-cs-fixer' => '~2.1.1',
                'lusitanian/oauth' => '~0.8.10',
                'sebastian/phpcpd' => '2.0.4',
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
        file_put_contents('composer.json', json_encode($composer, JSON_PRETTY_PRINT |  JSON_UNESCAPED_SLASHES));

        $shippable = [
            'branches' => [ 'only' => ['staging', 'production']],
            'build' => [
                'pre_ci_boot' => [
                  'image_name' => 'mygento/deployer',
                  'image_tag' => 'v1-full',
                  'pull' => true,
                ],
                'ci' => [
                  'apt-get install libxml2-utils',
                  'find . -path ./vendor -prune -o -name \'*.xml\' -print -exec xmllint --noout {} \;',
                  'composer self-update',
                  'bundle install',
                  '/root/.composer/vendor/bin/parallel-lint .',
                  'if [[ $IS_PULL_REQUEST == true ]]; then npm install --production; fi',
                  'if [[ $IS_PULL_REQUEST == true ]]; then NODE_ENV=production gulp lint; fi',
                ],
                'on_success' => [
                    'if [[ $IS_PULL_REQUEST != true ]]; then mina $BRANCH deploy_all; fi',
                ],
            ],
        ];

        $yaml = Yaml::dump($shippable, 3);

        file_put_contents('.shippable.yaml', $yaml);

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

task :staging do
  set :user, '$name'
  set :domain, 'host.ru'
  set :deploy_to, "/var/www/$name/host.ru"
  set :branch, 'staging'
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
  # uncomment this line to make sure you pushed your local branch to the remote origin
  # invoke :'git:ensure_pushed'
  deploy do
    # Put things that will set up an empty directory into a fully set-up
    # instance of your project.
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

        $gem = <<<GEM
source 'https://rubygems.org'
gem 'mina', git: 'https://github.com/luckyraul/mina.git', branch: 'relative_path'
gem 'scss_lint', require: false
GEM;

        file_put_contents('Gemfile', $gem);

        if (!is_dir('config')) {
            mkdir('config');
        }
        file_put_contents('config/deploy.rb', $mina);

        if (!is_dir('app')) {
            mkdir('app');
        }

        if (!is_dir('app/etc')) {
            mkdir('app/etc');
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
    'Dotdigitalgroup_Email' => 0,
    'Magento_WishlistAnalytics' => 1,
    'Mygento_Configsync' => 1,
    'Shopial_Facebook' => 0,
    'Temando_Shipping' => 0,
  ),
);
CONFIG;
        file_put_contents('app/etc/config.php', $config);
    }
}
