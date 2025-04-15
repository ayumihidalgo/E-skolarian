# E-SKOLARIAN

<div align="center">
  <h3>Tech Stack</h3>
  <img src="https://img.shields.io/badge/Laravel-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel">
  <img src="https://img.shields.io/badge/HTML5-E34F26?style=for-the-badge&logo=html5&logoColor=white" alt="HTML5">
  <img src="https://img.shields.io/badge/CSS3-1572B6?style=for-the-badge&logo=css3&logoColor=white" alt="CSS3">
  <img src="https://img.shields.io/badge/JavaScript-F7DF1E?style=for-the-badge&logo=javascript&logoColor=black" alt="JavaScript">
  <img src="https://img.shields.io/badge/Tailwind_CSS-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white" alt="Tailwind CSS">
</div>

<div align="center">
  <h3>
    <a href="#about">About</a> •
    <a href="#features">Features</a> •
    <a href="#requirements">Requirements</a> •
    <a href="#installation">Installation</a> •
    <a href="#usage">Usage</a> •

  </h3>
</div>


## About

---

## Features

---

## Requirements

- PHP >= 8.1
- Composer
- Node.js and npm
- MySQL or another Laravel-supported database

## Installation

```bash
# Clone the repository
git clone 

# Install PHP dependencies
composer install

# Install NPM dependencies
npm install

# Create a copy of your .env file
cp .env.example .env

# Generate an app encryption key
php artisan key:generate

# Configure your database in .env (or you can copy this)
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=eskolarian
DB_USERNAME=root
DB_PASSWORD=

# Run database migrations
php artisan migrate

# Compile assets
npm run dev

# Run server
php artisan serve
```

2. Update the php.ini File
Locate the php.ini file used by your PHP installation. You can find it by running the following command in your terminal:

``` bash 
php --ini
```
Open the php.ini file in a text editor. 

Remove the semicolon of the following:
- ;extension=zip
- ;extension=mbstring
- ;extension=fileinfo
- ;extension=curl

3. Restart Your Web Server using 
```bash
php artisan serve
```

## Usage

<details>
<summary>Click to expand usage instructions</summary>

1. Start the Laravel development server:
   ```
   php artisan serve
   ```
2. Start the compile of assets:
  ```
  npm run dev
  ```
3. Access the web interface at `http://localhost:8000`
4. Make sure apache and mysql is running when you are using XAMPP
5. Keep both 1 and 2 running on terminal during development


</details>

---

