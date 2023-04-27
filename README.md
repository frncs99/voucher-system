#### VOUCHER SYSTEM (Single Page Web Application)
- User can log in and create/delete their own unique voucher codes.
- User can be under a group and can have maximum of 10 vouchers.
- Group Admin User can view/export all vouchers of users that belong to the group they are managing.
- Group Admin User can assign/remove a user from the group they are managing.
- Super Admin User can view/export all vouchers.
- Super Admin User can create/rename/delete a group.
- Super Admin User can assign/remove a user from any group.
- Super Admin User can assign/remove a group admin user from any group/s.

#### PREREQUISITES:
- PHP 8+
- GIT https://git-scm.com/downloads
- Composer https://getcomposer.org/download/
- NodeJS/NPM https://nodejs.org/en/download/

#### GUIDE TO SETUP THE PROJECT:
- Copy the .env.example file and rename it to the .env file (modify database and mail config first)
- Execute the command `composer install` to insall the project dependencies
- Execute the command `php artisan key:generate` to generate environment app key
- Execute the command `php artisan migrate` to run the database migrations
- Execute the command `php artisan db:seed` to initially load the database tables
- Execute the command `npm install` to insall the (front) project dependencies
- Execute the command `npm run build` to build the UI
- Execute the command `php artisan serve`

Go to http://127.0.0.1:8000/

Use the following credentials (seeded data)
- Super Admin Email: `superadmin@example.com`
- Group Admin Email: `groupadmin_01@example.com` or `groupadmin_02@example.com`

All seeded users have
- Password: `password`

#### PROJECT TECHNOLOGIES
- MVC Architecture with Service Container
- Migrations, Seeders, and Factories
- Roles and Permissions
- Laravel Inertia with Vue3
- Tailwind CSS

#### COMING SOON
- Separate Log in Page for User and Admin
- Filter vouchers list by groups
- Unit Testing
- Broadcasting for real time changes
