# Update Chocolatier

### update

```bash
# Stop nginx
service nginx stop;

# Go to the web root
cd /usr/share/nginx/html/Chocolatier;

# Backup the DB and application files
sudo -u nginx php artisan backup:run;

# Pull the latest code
sudo -u nginx git pull origin master;

# Check out to the latest tag
sudo -u nginx git checkout $(git describe --tags $(git rev-list --tags --max-count=1));

# Install composer dependencies
sudo -u nginx composer install;

# Configure environment settings that have changed
sudo -u nginx vi .env;

# Run DB Migrations
sudo -u nginx php artisan migrate --force;
```