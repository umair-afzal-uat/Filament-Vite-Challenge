<p align="center">
<a href="https://laravel.com" target="_blank">
<img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo">
</a>
</p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About This Project
This is a Laravel-based application that integrates:

- **Filament**: A powerful admin panel for Laravel.
- **Vite**: A modern frontend build tool for asset management.

The project demonstrates how to set up Filament v4 with Laravel and use Vite to bundle assets efficiently. The build process generates optimized CSS and JavaScript files located in the `public/build` directory.

## Features
- **Filament Admin Panel**: A customizable and user-friendly admin interface.
- **Vite Integration**: Fast and efficient asset bundling with hot module replacement (HMR) support.
- **Laravel Framework**: Built on the latest version of Laravel for robust backend functionality.
- **Responsive Design**: Modern CSS for a seamless user experience.

## Prerequisites
Before you begin, ensure you have the following installed:

- PHP >= 8.1
- Composer
- Node.js >= 18.x
- npm or yarn
- MySQL or another supported database

## Installation
Follow these steps to set up the project locally:

### Step 1: Clone the Repository
```sh
git clone https://github.com/your-username/filament-vite-challenge.git
cd filament-vite-challenge
```

### Step 2: Install Dependencies
Install PHP dependencies using Composer:
```sh
composer install
```

Install JavaScript dependencies using npm:
```sh
npm install
```

### Step 3: Configure Environment
Copy the `.env.example` file to `.env` and update the environment variables:
```sh
cp .env.example .env
```

Update the `.env` file with your database credentials:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_database_user
DB_PASSWORD=your_database_password
```

### Step 4: Generate Application Key
Generate the application key:
```sh
php artisan key:generate
```

### Step 5: Run Migrations
Run the database migrations to set up the schema:
```sh
php artisan migrate
```

### Step 6: Build Frontend Assets
Build the frontend assets using Vite:
```sh
npm run build
```

### Step 7: Start the Development Server
Start the Laravel development server:
```sh
php artisan serve
```

Access the application at:
[http://localhost:8000](http://localhost:8000)

Access the Filament admin panel at:
[http://localhost:8000/admin](http://localhost:8000/admin)

## Build Output
After running `npm run build`, the following assets are generated in the `public/build` directory:

- `manifest.json`
- `assets/app-GrX07NgV.css` (49.78 kB, 8.64 kB)
- `assets/theme-C3lZM6e6.css` (49.97 kB, 8.67 kB)
- `assets/index-l0sNRNKZ.js` (0.00 kB, 0.02 kB)
- `assets/app-CqflisoM.js` (35.09 kB, 14.13 kB)


## Contributing
If you'd like to contribute to this project, follow these steps:

1. Fork the repository.
2. Create a new branch (`git checkout -b feature/YourFeatureName`).
3. Commit your changes (`git commit -m 'Add some feature'`).
4. Push to the branch (`git push origin feature/YourFeatureName`).
5. Open a pull request.

## License
This project is licensed under the MIT License. Feel free to modify and distribute it as needed.

## Contact
For questions or support, contact:

- **Name**: Umair Afzal
- **Email**: umair.afzal.uat@gmail.com

- **GitHub**: [Filament-Vite-Challenge](https://github.com/umair-afzal-uat/Filament-Vite-Challenge)

## Acknowledgments
- **Filament**: For providing an excellent admin panel package.
- **Vite**: For enabling fast and modern asset bundling.
- **Laravel**: For the robust PHP framework.