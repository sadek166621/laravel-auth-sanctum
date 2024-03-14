# Zero Config API Boilerplate with Laravel Sanctum
## Setup instructions
### Clone repository
```bash
git clone git@github.com:sayedulsayem/laravel-auth-sanctum.git
cd laravel-auth-sanctum
```
### Install dependency
```bash
composer install
```
### Copy `.env.example` to `.env` file
```bash
cp .env.example .env
```
### Connect Database
- Create a database in your mysql. e.g. laravel-auth-sanctum
- Place database credentials in you `.env` file
```env
DB_DATABASE=laravel-auth-sanctum
DB_USERNAME=<DATABASE-USER> #this is database user name
DB_PASSWORD=<DATABASE-PASSWORD> #this is database password
```
### Run database migration
```bash
php artisan migrate
```
### Generate App key
```bash
php artisan key:generate
```
### Run Application
```bash
php artisan serve
```