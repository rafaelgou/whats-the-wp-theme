# What's The WP Theme

A Wordpress theme discover. Working version [here](https://whats-the-wp-theme.rgou.net).

## Running locally

Install main dependencies:

- PHP/MySQL, PHP/MariaDB or PHP/Postgres
- [NodeJS](https://nodejs.org/en/download/)
- [Composer](https://getcomposer.org/doc/00-intro.md#installation-linux-unix-osx)

## Install

### The code

```bash
git clone https://github.com/rafaelgou/whats-the-wp-theme.git
composer install
cp .env.example .env
```

Fill database, enviroment, debug and url settings:

```env
APP_ENV=local
APP_KEY=LONG-RANDOM-STRING
APP_DEBUG=true
APP_URL=http://127.0.0.1/8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=wtwpt
DB_USERNAME=wtwpt_user
DB_PASSWORD=YOUR-STRONG-PASSWORD
```

### Database

You need a database (MySQL/MariaDB or Postgres). Create a user credentials and a database.

For MySQL it could be:

```sql
CREATE DATABASE `whats_the_wp_theme` COLLATE 'utf8_unicode_ci';
CREATE USER 'whats_the_wp_theme'@'%' IDENTIFIED BY 'YOUR-STRONG-PASSWORD';
GRANT ALL PRIVILEGES ON whats_the_wp_theme.* TO 'whats_the_wp_theme'@'%';
FLUSH PRIVILEGES;
```

### Migrating and running

Then you can run:

```bash
php artisan migrate
php artisan serve
```

You'll have the site available at [http://127.0.0.1:8000](http://127.0.0.1:8000).
