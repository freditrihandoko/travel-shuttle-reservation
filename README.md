# 🚐 Travel Shuttle Reservation System

![Laravel](https://img.shields.io/badge/Laravel-11-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
![Livewire](https://img.shields.io/badge/Livewire-3-FB70A9?style=for-the-badge&logo=livewire&logoColor=white)
![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white)

A modern travel shuttle reservation system built with Laravel 11, Livewire 3, and Tailwind CSS. This application allows users to book shuttle services between different cities and locations, with an admin panel for managing trips, reservations, and vehicles.

## ✨ Features

- 🚌 Simple shuttle reservation system
- 🎫 Dynamic seat selection
- 📍 Multiple pickup and drop-off locations
- 📱 Responsive design using Tailwind CSS
- ⚡ Real-time updates with Livewire
- 👥 Role-based access control using Spatie Laravel Permission (admin)
- 🔒 Secure authentication with Jetstream (admin)

## 🛠️ Prerequisites

- PHP ^8.2
- Composer
- Node.js & NPM
- MySQL/PostgreSQL
- Git

## ⚙️ Installation

1. Clone the repository
```bash
git clone https://github.com/freditrihandoko/travel-shuttle-reservation.git
cd travel-shuttle-reservation
```

2. Install PHP dependencies
```bash
composer install
```

3. Install and compile frontend dependencies
```bash
npm install
npm run dev
```

4. Configure environment variables
```bash
cp .env.example .env
php artisan key:generate
```

5. Set up your database in `.env` file
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

6. Run migrations and seeders
```bash
php artisan migrate
php artisan db:seed
```
This will create an admin user:
- Email: admin@gmail.com
- Password: admin

If you get an error doing 'php artisan migrate', please see here how to solve it (https://github.com/freditrihandoko/travel-shuttle-reservation/issues/1)

7. To assign additional roles using tinker
```bash
php artisan tinker
```
Then run:
```php
// Assign additional roles
$user = User::find(1);
$user->assignRole('trip-admin');

// Check user roles
$user->getRoleNames(); // ['trip-admin']

// Remove role
$user->removeRole('trip-admin');

// Add multiple roles
$user->syncRoles(['trip-admin', 'reservation-admin']);

//or you can make it directly super-admin
$user->hasRole('super-admin');
```


8. Start the development server
```bash
php artisan serve
```

## 🏗️ Database Structure

The system includes the following main tables:
- `users` - User Admin management
- `cities` - Available cities
- `pools` - Pickup/drop-off locations
- `routes` - Travel routes between pools
- `trips` - Scheduled trips
- `vehicles` - Available vehicles with seat configurations
- `seats` - Individual seats in vehicles
- `reservations` - Booking information
- `passengers` - Passenger details for each reservation

## 👥 Role System

The application uses three main roles:
- `super-admin`: Full access to all features
- `trip-admin`: Manages trips and vehicles
- `reservation-admin`: Handles reservations and passenger management

## 📸 Screenshots

![Index](https://i.ibb.co.com/DfqDRb4/Screenshot-2025-01-07-at-23-20-08.png)

![Reservation](https://i.ibb.co.com/CBwctVv/Screenshot-2025-01-07-at-23-29-04.png)

![Reservation 2](https://i.ibb.co.com/Qv1Lqzj/Screenshot-2025-01-07-at-23-30-23.png)

![Reservation 3](https://i.ibb.co.com/DrdFtGv/Screenshot-2025-01-07-at-23-31-26.png)

![Admin Dashboard](https://i.ibb.co.com/bNG1b7H/Screenshot-2025-01-07-at-23-35-49.png)

![Vehicles](https://i.ibb.co.com/b7Bv75y/Screenshot-2025-01-07-at-23-36-11.png)

![Trips](https://i.ibb.co.com/g6z4Sth/Screenshot-2025-01-07-at-23-41-01.png)

![Reservation Admin](https://i.ibb.co.com/kSZCX7G/Screenshot-2025-01-07-at-23-42-26.png)

## 🚧 Current Limitations & Future Development

### Admin Management
- Need to implement user admin management interface for role assignment
- Add user activity logging
- Enhance permission granularity

### Notifications
- SMS notifications for booking confirmations
- WhatsApp integration for updates and reminders
- Email notification system enhancement

### Payment System
The system currently lacks payment integration. Future development could include:
- Payment gateway integration (Midtrans, Xendit, etc.)
- Multiple payment method support
- Automatic receipt generation
- Refund processing system

Feel free to contribute to these areas or adapt them according to your needs.


## 🔐 Security

- Authentication using Laravel Jetstream
- Role-based access control using Spatie Laravel Permission
- CSRF protection

## 📄 License

This project is open-sourced software licensed under the [MIT license](LICENSE.md).

## 🤝 Contributing

Contributions, issues, and feature requests are welcome! Feel free to check the [issues page](../../issues).

## 👏 Acknowledgments

- [Laravel](https://laravel.com)
- [Livewire](https://livewire.laravel.com)
- [Tailwind CSS](https://tailwindcss.com)
- [Spatie Laravel Permission](https://spatie.be/docs/laravel-permission)
- [Jetstream](https://jetstream.laravel.com)




