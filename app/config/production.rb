set :domain,      "46.226.109.127"
set :deploy_to,   "/srv/datadisk01/web/cap3/cosmetics/site/deploys"
set :app_path,    "app"

set :repository,  "git@github.com:kwattro/makeupcosmetics.git"
set :scm,         :git
# Or: `accurev`, `bzr`, `cvs`, `darcs`, `subversion`, `mercurial`, `perforce`, or `none`

set :model_manager, "doctrine"
# Or: `propel`

role :web,        domain                         # Your HTTP server, Apache/etc
role :app,        domain, :primary => true       # This may be the same as your `Web` server

set  :keep_releases,  3
set  :use_sudo, true
set  :user, "angusyoung"
set  :ssh_options, { :forward_agent => true }
default_run_options[:pty] = true

#Symfony2 Config
set :shared_files,      ["app/config/parameters.yml"]
set :shared_children,     [app_path + "/logs", web_path + "/uploads", "vendor"]
set :use_composer, true
set :update_vendors, true

# Be more verbose by uncommenting the following line
logger.level = Logger::MAX_LEVEL

task :upload_parameters do
  origin_file = "app/config/parameters/parameters_prod.yml"
  destination_file = shared_path + "/app/config/parameters.yml" # Notice the
  shared_path

  try_sudo "mkdir -p #{File.dirname(destination_file)}"
  try_sudo "chown -R angusyoung:www-data #{shared_path}"
  top.upload(origin_file, destination_file)
end



before "symfony:composer:install", "upload_parameters"
before "symfony:composer:update", "upload_parameters"

before 'symfony:composer:install', 'composer:copy_vendors'
before 'symfony:composer:update', 'composer:copy_vendors'

namespace :composer do
  task :copy_vendors, :except => { :no_release => true } do
    capifony_pretty_print "--> Copy vendor file from previous release"

    run "vendorDir=#{current_path}/vendor; if [ -d $vendorDir ] || [ -h $vendorDir ]; then cp -a $vendorDir #{latest_release}/vendor; fi;"
    capifony_puts_ok
  end
end

before :deploy, "deploy:copy_database_config"

namespace :deploy do
task :copy_database_config do
    try_sudo "chown -R angusyoung:www-data #{shared_path}"
    end
end


set :writable_dirs,       ["app/cache", "app/logs"]
set :webserver_user,      "angusyoung"
set :permission_method,   :chown
set :use_set_permissions, true

