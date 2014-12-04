# Simple Role Syntax
# ==================
# Supports bulk-adding hosts to roles, the primary server in each group
# is considered to be the first unless any hosts have the primary
# property set.  Don't declare `role :all`, it's a meta role.

role :app, %w{angusyoung@217.70.189.241}


# Extended Server Syntax
# ======================
# This can be used to drop a more detailed server definition into the
# server list. The second argument is a, or duck-types, Hash and is
# used to set extended properties on the server.

server '217.70.189.241', user: 'angusyoung', roles: %w{app}, my_property: :my_value



set :deploy_to, '/srv/webdisk/web/www/makeupcosmetics/'
set :composer_install_flags, '--no-dev --no-interaction --prefer-source'

set :keep_releases, 5

before 'deploy:symlink:shared', "setup:upload_production_params"
before :deploy, "setup:set_write_perms"
# before 'deploy:cleanup', "setup:set_write_perms"
before 'deploy:cleanup', "setup:free_releases"
# after :deploy, "setup:set_write_perms"
after 'deploy:cleanup', "setup:free_write_perms"


# Custom SSH Options
# ==================
# You may pass any option but keep in mind that net/ssh understands a
# limited set of options, consult[net/ssh documentation](http://net-ssh.github.io/net-ssh/classes/Net/SSH.html#method-c-start).
#
# Global options
# --------------
  set :ssh_options, {
#    keys: %w(/home/rlisowski/.ssh/id_rsa),
#    forward_agent: true,
#    auth_methods: %w(password)
 }
#
# And/or per server (overrides global)
# ------------------------------------
# server 'example.com',
#   user: 'user_name',
#   roles: %w{web app},
#   ssh_options: {
#     user: 'user_name', # overrides user setting above
#     keys: %w(/home/user_name/.ssh/id_rsa),
#     forward_agent: false,
#     auth_methods: %w(publickey password)
#     # password: 'please use keys'
#   }
