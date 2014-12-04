namespace :setup do

  desc "Upload parameters.yml file."
  task :upload_params do
    on roles(:app) do
      execute "mkdir -p #{shared_path}/app/config"
      upload! StringIO.new(File.read("app/config/parameters/parameters_staging.yml")), "#{shared_path}/app/config/parameters.yml"
      execute "ln -sf #{shared_path}/app/config/parameters.yml #{release_path}/app/config/parameters.yml"
      execute "rm -f #{release_path}/app/bootstrap.php.cache > /dev/null"
      execute "touch #{release_path}/app/bootstrap.php.cache > /dev/null"
    end
  end

  desc "Upload parameters.yml file."
  task :upload_production_params do
    on roles(:app) do
      execute "mkdir -p #{shared_path}/app/config"
      upload! StringIO.new(File.read("app/config/parameters/parameters_prod.yml")), "#{shared_path}/app/config/parameters.yml"
      execute "ln -sf #{shared_path}/app/config/parameters.yml #{release_path}/app/config/parameters.yml"
      execute "rm -f #{release_path}/app/bootstrap.php.cache > /dev/null"
      execute "touch #{release_path}/app/bootstrap.php.cache > /dev/null"
    end
  end

  desc "Makes cache and logs dir writable"
  task :set_write_perms do
    on roles(:app) do
      execute "sudo chmod -R 777 #{release_path}/app/cache"
      # execute "sudo chmod -R 777 #{shared_path}/app/logs"
    end
  end

  desc "Makes cache and logs dir writable"
    task :free_write_perms do
      on roles :all do
        execute "sudo chmod -R 777 #{release_path}/app/cache"
        # execute "sudo chmod -R 777 #{shared_path}/app/logs"
      end
    end

  desc 'Clean up old releases'
  task :free_releases do
  on release_roles :all do |host|
  releases = capture(:ls, '-x', releases_path).split
  if releases.count >= fetch(:keep_releases)
  info t(:keeping_releases, host: host.to_s, keep_releases: fetch(:keep_releases), releases: releases.count)
  directories = (releases - releases.last(fetch(:keep_releases)))
  if directories.any?
  directories_str = directories.map do |release|
  releases_path.join(release)
  end.join(" ")
  execute "sudo chmod -R 777 #{directories_str}"
  else
  info t(:no_old_releases, host: host.to_s, keep_releases: fetch(:keep_releases))
  end
  end
  end
  end


end
