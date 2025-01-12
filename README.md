# Education System

## Introduction to the Educational Platform Project
The **Educational Platform** is a comprehensive solution designed to streamline and enhance the learning experience by leveraging the power of modern technology. This platform features a role-based structure with four distinct roles: **Admin**, **Manager**, **Teacher**, and **Student**. Each role comes with tailored permissions to ensure secure and efficient access to the platform’s features.

To manage access and ensure robust security, the platform utilizes two guards: **API** and **Teacher-API**, facilitating role-specific authentication and authorization mechanisms.

The platform also incorporates a set of CRUD operations to manage various resources efficiently.

## CRUD Functionalities:

### Auth
- Handles user authentication on the platform.
- Login can be performed through either the **API** or the **Teacher-API**.

### User
- Managed by the **Admin** to:
  - Add new users to the platform.
  - Edit user information.
  - Delete users.

### Role
- Managed by the **Admin** to:
  - Add new roles.
  - Edit existing roles.
  - Delete roles.

### Teacher
- Managed by the **Admin** to:
  - Add new teachers.
  - Edit teacher information.
  - Delete teachers.

### Category
- Managed by the **Admin** to:
  - Add new categories.
  - Edit existing categories.
  - Delete categories.

### Course
- Managed by **Teachers** to:
  - Add new courses.
  - Edit course details.
  - Delete courses.

### Task
- Managed by **Teachers** to:
  - Add new tasks.
  - Edit task details.
  - Delete tasks.
- **Students** can upload completed tasks as files.

### Materials
- Managed by **Teachers** to:
  - Add materials to a specific course.
  - Edit existing materials.
  - Delete materials.
  # Installation

## Prerequisites
Ensure you have the following installed on your machine:

- **XAMPP**: For running MySQL and Apache servers locally.
- **Composer**: For PHP dependency management.
- **PHP**: Required for running Laravel.
- **MySQL**: Database for the project.
- **Postman**: Required for testing the requests.


## step to run the project

### 1. Add Database Name in phpMyAdmin
Create a new database in **phpMyAdmin** and note its name.

### 2. Update the `.env` File
Add the database name to the `DB_DATABASE` field in the `.env` file.

### 3. Run the Project in Terminal Using These Steps:

#### 1. Clear Configuration Cache
Clear the cached configuration to ensure the `.env` file is updated:
```bash
php artisan config:clear
```
#### 2. Cache Configuration
Cache the current configuration to enhance performance:
```bash
php artisan config:cache
```

#### 3.  Run Migrations
Run migrations to set up or update the database schema:
```bash
php artisan migrate
```

#### 4.  Migrate Seeder
```bash
php artisan db:seed --class=PermissionSeeder
php artisan db:seed --class=UserSeeder
php artisan db:seed --class=TeacherSeeder
php artisan db:seed --class=CategorySeeder
php artisan db:seed --class=CourseSeeder
php artisan db:seed --class=Course_UserSeeder
php artisan db:seed --class=MaterialSeeder
php artisan db:seed --class=TaskSeeder
```

## doc of postman is
https://documenter.getpostman.com/view/34555205/2sAYBa8omR