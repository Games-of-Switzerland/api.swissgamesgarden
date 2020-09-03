# config valid only for current version of Capistrano
lock '3.5.0'

set :application, 'gos'
set :repo_url, 'git@github.com:Games-of-Switzerland/gos-server.git'

set :app_path, "web"

set :docker_app_name, -> {
  [fetch(:application), fetch(:stage)].join('_')
}
set :docker_app_service, 'app'
set :docker_containers, 'app db mailcatcher elasticsearch'

server 'gos.museebolo.ch', port: '44144', user: 'deploy', roles: %w{app db web}

# Link environments files
set :linked_files, fetch(:linked_files, []).push("#{fetch(:app_path)}/sites/default/prod.settings.php", "docker-compose.override.yml")

# Link dirs files and private-files
set :linked_dirs, fetch(:linked_dirs, []).push("#{fetch(:app_path)}/sites/default/files")

# Default value for :scm is :git
set :scm, :git

# Default value for :pty is false
# set :pty, true

# Default value for :format is :pretty
# set :format, :pretty

# Default value for keep_releases is 5
# set :keep_releases, 3

# Set SSH options
set :ssh_options, {
  forward_agent: true
}

set :ssh_options, keys: ["config/deploy_id_rsa_enc_travis"] if File.exist?("config/deploy_id_rsa_enc_travis")

namespace :deploy do
  desc '(re)Start docker containers'
  task :restart do
    on roles(:app) do
      within current_path do
        # Build then start service
        execute :docker_compose, 'up', '-d', '--no-deps', '--build', fetch(:docker_containers)
      end
    end
  end

  desc 'Stop all docker containers'
  task :stop do
    on roles(:app) do
      within current_path do
        execute :docker_compose, 'down'
      end
    end
  end

  desc 'Cleanup docker storage (container, imaged, ...)'
  task :cleanup do
    on roles(:app) do
      within current_path do
        execute :docker, 'system', 'prune', '-f', raise_on_non_zero_exit: false
      end
    end
  end

  desc 'Run the Update scripts on container'
  task :update do
    on roles(:app) do
      within current_path do
        execute :docker_compose, 'exec', '-T', fetch(:docker_app_service), './scripts/drupal/update.sh'
        execute :docker_compose, 'exec', '-T', fetch(:docker_app_service), './scripts/drupal/elasticsearch.sh'
      end
    end
  end

  namespace :permissions do
    desc 'Set recommended Drupal permissions'
    task :recommended do
      on roles(:app) do
        within release_path.join(fetch(:app_path)) do
          execute :chmod, '-R', '555', '.'

          # Remove execution for files, keep execution on folder.
          execute 'find', './ -type f -executable -exec chmod -x {} \;'
          execute 'find', './ -type d -exec chmod +x {} \;'
        end
      end
    end

    desc 'Set cleanup permissions to allow deletion of releases'
    task :cleanup do
      on roles(:app) do
        releases = capture(:ls, '-x', releases_path).split
        valid, invalid = releases.partition { |e| /^\d{14}$/ =~ e }

        if valid.count >= fetch(:keep_releases)
          directories = (valid - valid.last(fetch(:keep_releases))).map do |release|
            releases_path.join(release)
          end
          if test("[ -d #{current_path} ]")
            current_release = capture(:readlink, current_path).to_s
            if directories.include?(current_release)
              directories.delete(current_release)
            end
          end
          if directories.any?
            directories.each_slice(100) do |directories_batch|
              execute :chmod, '-R' ,'ug+w', *directories_batch
            end
          end
        end
      end
    end

    desc 'Initalize shared path permissions'
    task :writable_shared do
      on roles(:app) do
        within shared_path do
          # "web/sites/default/files" is a shared dir and should be writable.
          execute :chmod, '-R', '775', "#{fetch(:app_path)}/sites/default/files"

          # Remove execution for files, keep execution on folder.
          execute 'find', "#{fetch(:app_path)}/sites/default/files", '-type f -executable -exec chmod -x {} \;'
          execute 'find', "#{fetch(:app_path)}/sites/default/files", '-type d -exec chmod +sx {} \;'
        end
      end
    end
  end

  after :publishing, 'deploy:restart'
  after 'deploy:restart', 'deploy:update'

  # Ensure permissions are properly set.
  after 'deploy:update', "deploy:permissions:recommended"

  # Fix the release permissions (due to Drupal restrictive permissions)
  # before deleting old release.
  before :cleanup, "deploy:permissions:cleanup"
end
