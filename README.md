# POS Skincare

A web-based point of sale application for a skincare store, built with Laravel, Blade, Bootstrap, and MySQL.

## Requirements

- PHP 8.4.1 or newer in the PHP 8.x line, with the extensions required by Laravel.
- Composer.
- MySQL and Node.js/npm.

The locked Symfony dependencies require PHP 8.4.1. In Laragon, select a compatible PHP version before running Composer or Artisan. Check it with `php -v`.

## Local setup (Windows / Laragon)

1. Create an empty MySQL database named `pos_skincare` (or set another name in the local `.env`).
2. Install PHP dependencies and create a local environment file from the safe project template:

   ```powershell
   composer install
   Copy-Item config/pos-skincare.env.example .env
   ```

   If `.env` already exists, keep it and update only the local database settings. Never commit `.env`.

3. Generate the application key, migrate the database, and build the frontend assets:

   ```powershell
   php artisan key:generate
   php artisan migrate
   npm install
   npm run build
   php artisan storage:link
   ```

4. Create the first administrator interactively. The password is entered as a hidden prompt and is not stored in source code:

   ```powershell
   php artisan pos:admin:create
   ```

   This command refuses to create another first administrator once one exists. Administrators can create cashier accounts from the Users page.

5. Start the app and sign in:

   ```powershell
   php artisan serve
   ```

   Open `http://127.0.0.1:8000`. After signing in, configure the store details and receipt footer in Settings.

## Validation

Run the test suite with the project PHP version:

```powershell
php artisan test
```

The test configuration uses an in-memory SQLite database. Checkout and return tests therefore do not use the operational MySQL database.

## Account setup

Public registration is disabled. Use the interactive first-admin command for a new installation; administrators manage additional accounts from the application. The normal database seeder does not create sample users or shared credentials.
