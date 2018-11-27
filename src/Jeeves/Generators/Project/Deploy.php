<?php

namespace Mygento\Jeeves\Generators\Project;

class Deploy
{
    public function generateDeployer($name, $repo)
    {
        $mina = <<<RUBY
require 'mina/deploy'
require 'mina/git'

set :application_name, '$name' # application  name
set :repository, '$repo'

set :shared_dirs, ['var/log','var/backups','var/report','pub/media', 'var/import','var/export']
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
        return $mina;
    }

    public function generateGems()
    {
        $gem = <<<GEM
source 'https://rubygems.org'

gem 'mina', git: 'https://github.com/luckyraul/mina.git', branch: 'relative_path'
gem 'scss_lint', require: false

GEM;
        return $gem;
    }

    public function generateCI()
    {
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
        return $shippable;
    }
}
