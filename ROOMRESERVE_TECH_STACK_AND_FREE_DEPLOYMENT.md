# RoomReserve Tech Stack and Free Deployment Guide

## Tech Stack

### Backend
- PHP 8.3
- Laravel 13
- Eloquent ORM
- Laravel Notifications (mail)
- Role-based middleware (guest/admin)

### Frontend
- Blade templating
- Tailwind CSS 4 + custom component CSS
- Alpine.js (menu and slider interactions)
- Vite 8 for asset bundling

### Data Layer
- MySQL/MariaDB
- Laravel migrations and seeders
- Core models: User, Room, Booking, Feedback

### Auth and Security
- Session authentication
- Password hashing with Laravel Hash
- Email code-based password reset (6-digit code)
- CSRF protection in forms

### Booking and Hotel Features
- Room availability filtering
- Reservation management (create/update/cancel)
- Booking total bill visibility
- Booking availability state visibility (available/unavailable)
- Guest feedback and public review display
- Highlighted reserved dates in room booking flow
- Email notifications:
  - Booking confirmation
  - Booking updated
  - Booking cancelled
  - 24-hour booking reminder
  - Password reset code

## Environment Variables

Set these in local .env and production host settings.

```env
APP_NAME=RoomReserve
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-app-url

DB_CONNECTION=mysql
DB_HOST=your-db-host
DB_PORT=3306
DB_DATABASE=your-db-name
DB_USERNAME=your-db-user
DB_PASSWORD=your-db-password

MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your-mail-username
MAIL_PASSWORD=your-mail-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=no-reply@roomreserve.app
MAIL_FROM_NAME="RoomReserve"
```

## Free Deployment Option

Recommended stack:
- Render (web app)
- Free MySQL provider (any reliable free host)
- Mailtrap/Brevo free tier for SMTP

### 1. Push to GitHub
- Commit your RoomReserve project and push to a GitHub repository.

### 2. Create a free database
- Create a MySQL database from your preferred free provider.
- Copy host, port, db name, username, password.

### 3. Deploy on Render
Create a new Web Service from your repo.

Build command:
```bash
composer install --no-dev --optimize-autoloader; npm ci; npm run build; php artisan config:cache; php artisan route:cache; php artisan view:cache
```

Start command:
```bash
php artisan migrate --force; php artisan serve --host 0.0.0.0 --port $PORT
```

### 4. Add environment variables
- Add APP_*, DB_*, and MAIL_* variables in Render.

### 5. Enable scheduled reminders
RoomReserve uses a scheduler entry for 24-hour reminders:
- Command: `php artisan bookings:send-reminders`
- Frequency: hourly

If your free host has no cron feature, use an external free cron trigger service.

## Post-Deployment Checklist

1. Open the app URL and confirm home page renders.
2. Register and log in as guest.
3. Create a booking and verify:
- Total bill displays
- Availability appears in booking list
- Confirmation email is sent
4. Update/cancel booking and confirm notification emails.
5. Request forgot password, receive 6-digit code, and reset password.
6. Run `php artisan bookings:send-reminders` manually once to verify reminder emails.

## Local Run Commands

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
npm run build
php artisan serve
```

Manual reminder run:

```bash
php artisan bookings:send-reminders
```
