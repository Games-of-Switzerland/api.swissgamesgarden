# Load DSL and set up stages.
require "capistrano/setup"

# Include default deployment tasks.
require "capistrano/deploy"

# Load custom tasks from `lib/capistrano/tasks` if you have any defined.
Dir.glob('config/capistrano/tasks/*.rake').each { |r| import r }
