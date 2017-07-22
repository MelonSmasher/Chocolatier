# Install Chocolatier

### Requirements:

- php >= 7.0.0
- Redis
- MariaDB/MySQL (tested on MariaDB 10.1)
- [Yarn](https://yarnpkg.com/) -- For development*
- [composer](https://getcomposer.org/)
- NGINX or Apache (tested on NGINX)

PHP Packages:

- php-pecl-redis
- php-pdo
- php-mysqlnd
- php-mcrypt
- php-mbstring
- php-gd
- php-xml
- php-fpm (NGINX only)

### Install:

* [Install NGINX](https://github.com/MelonSmasher/NginxInstaller)

* Install MariaDB

* Install Redis

* Install PHP and extensions

* Initialize the DB

```mysql
create database chocolatier;
CREATE USER 'chocolatier'@'localhost' IDENTIFIED BY 'SOMESTRONGPASSWORD';
GRANT ALL PRIVILEGES ON chocolatier.* To 'chocolatier'@'localhost';
FLUSH PRIVILEGES;
```

* Initialize ORM

```bash
# Create a package and backup directory
sudo mkdir -p /home/nginx/packages;
sudo mkdir -p /home/nginx/chocolatier-backup;

# Set the right permissions
sudo chown -R nginx:nginx /home/nginx;
sudo chown -R nginx:nginx /usr/share/nginx/html;

# Go to the web root
cd /usr/share/nginx/html/;

# Clone Repo with composer
sudo -u nginx composer create-project melonsmasher/chocolatier Chocolatier --keep-vcs;

# Link package and backup dirs
sudo -u nginx ln -s /home/nginx/packages/ /usr/share/nginx/html/Chocolatier/storage/app/packages;
sudo -u nginx ln -s /home/nginx/chocolatier-backup/ /usr/share/nginx/html/Chocolatier/storage/app/chocolatier-backup;

# Get into the project
cd Chocolatier;
```

* Configure environment settings

```bash
sudo -u nginx vi .env;
```

* DB Migrations

```bash
# Run DB Migrations
sudo -u nginx php artisan migrate --force;
```