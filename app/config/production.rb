server '46.226.109.127', :app, :web, :primary => true
set :domain,      "46.226.109.127"
set :deploy_to,   "/srv/datadisk01/web/cosmetics/site/deploys"
set  :use_sudo, true
set  :user, "angusyoung"
set  :ssh_options, { :forward_agent => true }
default_run_options[:pty] = true