# What's The WP Theme

A Wordpress theme discover.

## Running locally

Install main dependencies:

- PHP/MySQL, PHP/MariaDB or PHP/Postgres
- [NodeJS](https://nodejs.org/en/download/)
- [Composer](https://getcomposer.org/doc/00-intro.md#installation-linux-unix-osx)

## Install

```bash
git clone ???
composer install
node install
cp .env.example .env
```

### Database

You need a database (MySQL/MariaDB or Postgres). Create a user credentials and a database.

For MySQL it could be:

```sql
CREATE DATABASE `portfolio_resume` COLLATE 'utf8_unicode_ci';
CREATE USER 'portfolio_resume'@'%' IDENTIFIED BY 'YOUR-STRONG-PASSWORD';
GRANT ALL PRIVILEGES ON portfolio_resume.* TO 'portfolio_resume'@'%';
FLUSH PRIVILEGES;
```

Then you can run:

```bash
php artisan serve
```

You'll have the site available at [localhost:3000](http://localhost:3000).
