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
set :shared_children,     [app_path + "/logs", web_path + "/uploads", "vendor"]
set :use_composer, true
set :update_vendors, true

# Be more verbose by uncommenting the following line
logger.level = Logger::MAX_LEVEL