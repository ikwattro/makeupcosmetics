set :domain,      "192.168.56.10"
set :deploy_to,   "/home/ikwattro/www/sites/deploys"
set :app_path,    "app"


set :repository,  "https://github.com/kwattro/makeupcosmetics.git"
set :scm,         :git
# Or: `accurev`, `bzr`, `cvs`, `darcs`, `subversion`, `mercurial`, `perforce`, or `none`

set :model_manager, "doctrine"
# Or: `propel`

role :web,        domain                         # Your HTTP server, Apache/etc
role :app,        domain, :primary => true       # This may be the same as your `Web` server

set  :keep_releases,  3
set  :use_sudo, true
set  :user, "ikwattro"
set  :ssh_options, { :forward_agent => true }
default_run_options[:pty] = true

#Symfony2 Config
set :shared_files,      ["app/config/parameters.yml"]
set :shared_children,     [app_path + "/logs", web_path + "/uploads", "vendor", "/media"]
set :use_composer, true
set :update_vendors, true

# Be more verbose by uncommenting the following line
logger.level = Logger::MAX_LEVEL

task :upload_parameters do
  origin_file = "app/config/parameters/parameters_staging.yml"
  destination_file = shared_path + "/app/config/parameters.yml" # Notice the
  shared_path

  try_sudo "mkdir -p #{File.dirname(destination_file)}"
  top.upload(origin_file, destination_file)
end

after "symfony:composer:install", "upload_parameters"

set :writable_dirs,       ["app/cache", "app/logs", "web/media", "web/uploads"]
set :webserver_user,      "angusyoung"
set :permission_method,   :chown
set :use_set_permissions, true