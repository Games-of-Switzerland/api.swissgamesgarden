# config valid only for current version of Capistrano
lock '3.5.0'

set :application, 'gos'
set :repo_url, 'git@github.com:Games-of-Switzerland/gos-server.git'

set :docker_app_name, -> {
  [fetch(:application), fetch(:stage)].join('_')
}
set :docker_app_service, 'prod'
set :docker_containers, 'prod db mail elasticsearch'

server 'gos.museebolo.ch', port: '44144', user: 'deploy', roles: %w{app db web}

set :app_path, "web"

# Link file docker-compose.override.yml
set :linked_files, fetch(:linked_files, []).push("#{fetch(:app_path)}/sites/default/prod.settings.php", "docker-compose.prod.yml", "docker-compose.override.yml")

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
        execute :docker_compose, 'down', fetch(:docker_containers)
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
        sleep 15
        execute :docker_compose, 'exec', '-T', fetch(:docker_app_service), './scripts/drupal/update.sh'
      end
    end
  end

  after :publishing, 'deploy:restart'
  after 'deploy:restart', 'deploy:update'
end
