# config valid only for Capistrano 3.1
lock '3.2.1'

set :application, 'makeupcosmetics'

set :repo_url, 'git@github.com:kwattro/makeupcosmetics.git'

# Default branch is :master
# ask :branch, proc { `git rev-parse --abbrev-ref HEAD`.chomp }.call

# Default deploy_to directory is /var/www/my_app
# set :deploy_to, '/var/www/my_app'

# Default value for :scm is :git
# set :scm, :git

# Default value for :format is :pretty
# set :format, :pretty

# Default value for :log_level is :debug
# set :log_level, :debug

# Default value for :pty is false
set :pty, true

# Default value for :linked_files is []
#set :linked_files, %w{app_path + "database.yml"}
set :linked_files, %w{app/config/parameters.yml}
# Default value for linked_dirs is []
# set :linked_dirs, %w{bin log tmp/pids tmp/cache tmp/sockets vendor/bundle public/system}
#set :linked_dirs,           [fetch(:log_path), fetch(:web_path) + "/uploads", fetch(:cache_path), fetch(:web_path) + "/media"]
set :linked_dirs, %w{app/logs web/uploads web/media}
# Default value for default_env is {}
# set :default_env, { path: "/opt/ruby/bin:$PATH" }

# Default value for keep_releases is 5
# set :keep_releases, 5
