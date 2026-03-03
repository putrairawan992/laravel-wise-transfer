# Laravel Transfer Dashboard

A secure, desktop-first transfer dashboard built with Laravel 12 and Bootstrap 5.

## Features
- **Authentication**: Secure login/logout.
- **Dashboard**: View account balance and recent transfers.
- **Send Money**: Multi-step transfer flow with review and confirmation.
- **Security**: AES-256 encryption for sensitive recipient data (account numbers, notes).

## Setup

1.  **Install Dependencies**
    ```bash
    composer install
    ```

2.  **Environment Setup**
    ```bash
    cp .env.example .env
    php artisan key:generate
    touch database/database.sqlite
    ```

3.  **Database & Seeding**
    ```bash
    php artisan migrate:fresh --seed
    ```

4.  **Run Application**
    ```bash
    php artisan serve
    ```

## Test Credentials

- **Email**: `daniel@example.com`
- **Password**: `password`

## Tech Stack
- **Framework**: Laravel 12 (PHP 8.2+)
- **Frontend**: Blade Templates + Bootstrap 5
- **Database**: SQLite (Configurable)
- **Encryption**: Laravel Encrypted Casts (AES-256-CBC)
