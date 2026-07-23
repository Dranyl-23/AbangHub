<div align="center">
  
# 🏠 AbangHub

**The most trusted platform connecting tenants with quality boarding houses and apartments in Digos City.**

[![Laravel](https://img.shields.io/badge/Laravel-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com)
[![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white)](https://tailwindcss.com/)
[![Alpine.js](https://img.shields.io/badge/Alpine.js-8BC0D0?style=for-the-badge&logo=alpine.js&logoColor=white)](https://alpinejs.dev/)

</div>

---

## 📖 About AbangHub

AbangHub is a modern, comprehensive property rental management system designed specifically for the local market in Digos City. It bridges the gap between property owners (Landlords) and renters (Tenants) by providing a secure, transparent, and hassle-free environment for finding properties, signing leases, managing maintenance requests, and processing payments.

## ✨ Key Features

### 🧑‍💼 For Tenants
* **Smart Property Search**: Browse and filter boarding houses, apartments, and condo units by price, location, and amenities.
* **Wishlists**: Save your favorite properties to review them later.
* **Digital Lease Signing**: E-sign rental agreements directly within the platform.
* **Online Rent Payments**: Pay rent securely via integrated payment gateways (GCash, Maya, Stripe).
* **Maintenance Requests**: Easily report and track issues or repairs needed in your unit.

### 🏢 For Landlords
* **Property Management**: List new properties, upload images, and manage availability status.
* **Financial Dashboard**: Track income, view expenses, and monitor occupancy rates in real-time.
* **Automated Reminders**: The system automatically reminds tenants of upcoming or overdue rent.
* **Tenant Screening & Applications**: Review and approve tenant rental applications.
* **Wallet System**: Easily withdraw your rental earnings directly to your bank account.

### 🛡️ Core System Features
* **Real-time Chat System**: Direct messaging between tenants and landlords for smooth communication.
* **Role-Based Access Control (RBAC)**: Secure routing and dashboards tailored for Tenants, Landlords, and Admins.
* **Google OAuth Integration**: Quick and easy sign-in using Google accounts.

## 💻 Technology Stack
* **Backend**: Laravel 11, PHP 8.2+
* **Frontend**: Blade Templates, Tailwind CSS, Alpine.js, Livewire
* **Database**: MySQL / SQLite (for development)
* **Authentication**: Laravel Breeze, Socialite (Google)
* **Icons**: Heroicons

## 🚀 Getting Started

Follow these instructions to get a copy of the project up and running on your local machine for development and testing purposes.

### Prerequisites
* PHP 8.2 or higher
* Composer
* Node.js & NPM
* Git

### Installation

1. **Clone the repository**
   ```bash
   git clone https://github.com/Dranyl-23/AbangHub.git
   cd AbangHub/AbangHub
   ```

2. **Install PHP dependencies**
   ```bash
   composer install
   ```

3. **Install JavaScript dependencies**
   ```bash
   npm install
   ```

4. **Copy the environment file and configure your database**
   ```bash
   cp .env.example .env
   ```
   *Update your `.env` file with your database credentials and API keys.*

5. **Generate an application key**
   ```bash
   php artisan key:generate
   ```

6. **Run database migrations and seeders**
   ```bash
   php artisan migrate --seed
   ```

7. **Link storage directory**
   ```bash
   php artisan storage:link
   ```

8. **Start the local development servers**
   ```bash
   # In terminal 1
   php artisan serve

   # In terminal 2
   npm run dev
   ```

Visit `http://localhost:8000` in your browser to view the application!


## 📜 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

