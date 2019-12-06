# config valid only for current version of Capistrano
lock '3.5.0'

set :application, 'gos'
set :repo_url, 'git@github.com:Games-of-Switzerland/gos-server.git'

# server 'ssh.domain.ltd', user: 'gos', roles: %w{app db web}

set :app_path, "web"
set :config_path, "config/d8/sync"

# Link file settings.php
set :linked_files, fetch(:linked_files, []).push("#{fetch(:app_path)}/sites/default/settings.php")

# Link dirs files and private-files
set :linked_dirs, fetch(:linked_dirs, []).push("#{fetch(:app_path)}/sites/default/files")

# Default value for :scm is :git
set :scm, :git

# Default value for :pty is false
# set :pty, true

# Default value for :format is :pretty
# set :format, :pretty

# Default value for :log_level is :debug
set :log_level, :debug

# Default value for default_env is {}
# set :default_env, { path: "/opt/ruby/bin:$PATH" }

# Default value for keep_releases is 5
set :keep_releases, 3

# Default value for keep_backups is 5
set :keep_backups, 3

set :ssh_options, {
  forward_agent: true
}

# Used only if composer.json isn't on root
# set :composer_working_dir, -> { fetch(:release_path).join(fetch(:app_path)) }

# Remove default composer install task on deploy:updated
# Rake::Task['deploy:updated'].prerequisites.delete('composer:install')
# Rake::Task['deploy:updated'];

namespace :deploy do
  after "deploy:check:directories", "drupal:db:backup:check"
  before :starting, "drupal:db:backup"
  before :failed, "drupal:db:rollback"

  after :updated, "drupal:maintenance:on"
  # Must updatedb before import configurations, E.g. when composer install new
  # version of Drupal and need updatedb scheme before importing new config.
  # This is executed without raise on error, because sometimes we need to do drush config-import before updatedb.
  after :updated, "drupal:updatedb:silence"
  # Remove the cache after the database update
  after :updated, "drupal:cache:clear"
  after :updated, "drupal:config:import"
  # Sometimes (due to Webform) we have to run the drush cim twice.
  after :updated, "drupal:config:import"
  after :updated, "drupal:updatedb"
  after :updated, "drupal:cache:clear"

  after :updated, "drupal:maintenance:off"

  after :updated, "drupal:permissions:recommended"
  after :updated, "drupal:permissions:writable_shared"

  before :cleanup, "drupal:db:backup:cleanup"

  before :cleanup, :fix_permission do
    on roles(:app) do
      releases = capture(:ls, '-xtr', releases_path).split
      if releases.count >= fetch(:keep_releases)
        directories = (releases - releases.last(fetch(:keep_releases)))
        if directories.any?
          directories_str = directories.map do |release|
            releases_path.join(release)
          end.join(" ")
          execute :chmod, '-R' ,'ug+w', directories_str
        end
      end
    end
  end

  task :bootstrap do
    on roles(:app) do
      is_symbolic_link = File.symlink?("#{current_path}")
      if !is_symbolic_link
        info "Recursively delete the current directory #{current_path} to prevent fail on symlink creation."
        execute :rm, "-rf", "#{current_path}"
      end
    end

    Rake::Task['drupal:db:backup'].clear
    Rake::Task['drupal:db:rollback'].clear
    Rake::Task['drupal:maintenance:on'].enhance [Rake::Task['drupal:bootstrap']]
    invoke 'deploy'
  end
end

after 'drupal:bootstrap', :fix_drupal_install do
  on roles(:app) do
    within release_path.join(fetch(:app_path)) do
      execute :drush, %(ev '\Drupal::entityManager()->getStorage("shortcut_set")->load("default")->delete();')
      execute :drush, %(ev '
        $user = user_load_by_name("#{fetch(:drupal_admin_username)}");
        $user->set("preferred_admin_langcode", "en");
        $user->save();
      ')
    end
  end
end
