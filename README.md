# Laravel Transfer Dashboard & Admin Panel

A comprehensive fintech dashboard featuring money transfers, crypto wallet checking, e-KYC with face recognition, and WhatsApp integration.

## Features

### User Features
- **Secure Authentication**: Login/Logout with role-based access.
- **Dashboard**: View account balance and recent transactions.
- **Send Money**: Multi-step transfer flow with review and secure confirmation.
- **Fiuu Payment**: Integration with Razer Merchant Services for payments.
- **Crypto Wallet**: Check ETH/BSC/Polygon wallet balances using Moralis API.
- **e-KYC**: Identity verification with document upload and **AI Face Recognition**.
- **Support**: Submit enquiries/tickets to admin.

### Admin Features
- **Dashboard**: Overview of system stats and transactions.
- **Transaction Management**: View and filter user transactions.
- **KYC Management**: Review, approve, or reject user KYC submissions.
- **WhatsApp Manager**: Broadcast messages to users via Twilio API.
- **Support Tickets**: Manage and reply to user enquiries.
- **Integration Status**: Monitor health of third-party APIs (Fiuu, Moralis, Twilio).

## Setup Instructions

1.  **Clone Repository**
    ```bash
    git clone <repository-url>
    cd laravel-transfer
    ```

2.  **Install Dependencies**
    ```bash
    composer install
    npm install && npm run build
    ```

3.  **Environment Configuration**
    Copy `.env.example` to `.env` and configure your database and API keys.
    ```bash
    cp .env.example .env
    php artisan key:generate
    ```

    **Required API Keys in `.env`:**
    ```env
    # Fiuu (Payment Gateway)
    RMS_MERCHANT_ID=your_merchant_id
    RMS_VERIFY_KEY=your_verify_key
    RMS_SECRET_KEY=your_secret_key
    RMS_ENVIRONMENT=sandbox

    # Moralis (Crypto API)
    MORALIS_API_KEY=your_moralis_key

    # Twilio (WhatsApp API)
    TWILIO_AUTH_SID=your_sid
    TWILIO_AUTH_TOKEN=your_token
    TWILIO_WHATSAPP_FROM=+14155238886
    ```

4.  **Database Migration & Seeding**
    Create a database (e.g., in MySQL or SQLite) and run:
    ```bash
    php artisan migrate:fresh --seed
    ```

5.  **Run Application**
    ```bash
    php artisan serve
    ```

## Face Dataset Import (Optional)

Jika Anda ingin mengimpor dataset wajah dalam jumlah besar (contoh: 10k gambar), jalankan worker Node.js dan command Laravel berikut:

1.  **Install dependency worker**
    ```bash
    cd scripts/face-worker
    npm install
    ```

2.  **Jalankan import**
    ```bash
    php artisan face:import-dataset "C:/Path/To/Dataset"
    ```

Struktur folder dataset harus mengikuti pola:
```
1234-Nama Lengkap/
  image1.jpg
```

## Default Credentials

**Admin Account:**
- **Email**: `admin@example.com`
- **Password**: `password`

**User Account:**
- **Email**: `putrairawan993@gmail.com`
- **Password**: `password`

## Troubleshooting

- **"Credentials do not match"**: Run `php artisan migrate:fresh --seed` to reset the database and users.
- **Face Recognition Slow**: Ensure your internet connection is stable as models are loaded from CDN/local.
- **WhatsApp Error**: Verify your Twilio SID/Token and ensure the "From" number is correct.

## Tech Stack
- **Framework**: Laravel 12 (PHP 8.2+)
- **Frontend**: Blade Templates + Bootstrap 5
- **AI/ML**: face-api.js (Client-side Face Recognition)
- **Integrations**: Fiuu (Razer), Moralis (Web3), Twilio (WhatsApp)
