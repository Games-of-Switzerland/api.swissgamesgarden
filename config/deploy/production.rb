# api.gos.ch
set :deploy_to, '/home/gos/www/api.gos.ch'

# set a branch for this release
set :branch, 'master'

# Map composer and drush commands
# NOTE: If stage have different deploy_to
# you have to copy those line for each <stage_name>.rb
# See https://github.com/capistrano/composer/issues/22
SSHKit.config.command_map[:composer] = shared_path.join("composer.phar")
SSHKit.config.command_map[:drush] = shared_path.join("vendor/bin/drush")
