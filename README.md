
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
  - Soft Delete.
  - Testing.

### Role
- Managed by the **Admin** to:
  - Add new roles.
  - Edit existing roles.
  - Delete roles.
  - Soft Delete.
  - Testing.

### Teacher
- Managed by the **Admin** to:
  - Add new teachers.
  - Edit teacher information.
  - Delete teachers.
  - Soft Delete.
  - Testing.

### Category
- Managed by the **Admin** to:
  - Add new categories.
  - Edit existing categories.
  - Delete categories.
  - Soft Delete.
  - Testing.

### Course
- Managed by **Teachers** to:
  - Add new courses.
  - Edit course details.
  - Delete courses.
  - Soft Delete.
  - Testing.

- Managed by **Managers** to:
  - Set course start time.
  - Set course end time.
  - Set registration start time.
  - Set registration end time.
  - Change the status of a course.

### Task
- Managed by **Teachers** to:
  - Add new tasks.
  - Edit task details.
  - Delete tasks.
  - Soft Delete.
  - Testing.
- **Students** can upload completed tasks as files.

### Materials
- Managed by **Teachers** to:
  - Add materials to a specific course.
  - Edit existing materials.
  - Delete materials.
  - Soft Delete.
  - Testing.
  # Installation

## Prerequisites
Ensure you have the following installed on your machine:

- **XAMPP**: For running MySQL and Apache servers locally.
- **Composer**: For PHP dependency management.
- **PHP**: Required for running Laravel.
- **MySQL**: Database for the project.
- **Postman**: Required for testing the requests.

## step to run the project :
  ### 1. Clone the Repository :
  ```bash
  git clone https://github.com/ayaalaji/education_system
  ```
  ### 2. Install Dependencies :
  ```bash
  composer install
  ```
  ### 3. Create Environment File :
  ```bash
  cp .env.example .env
  ```
  ### 4. Generate Application Key:
  ```bash
  php artisan key:generate 
  ```

  ### 5. Add Database Name in phpMyAdmin
  Create a new database in **phpMyAdmin** and note its name.

  ### 6. Update the `.env` File
  Add the database name to the `DB_DATABASE` field in the `.env`   file.

  ### 7. Run the Project in Terminal Using These Steps:

  #### 1. Clear Configuration Cache
  Clear the cached configuration to ensure the `.env` file is     updated:
  ```bash
  php artisan config:clear
  ```
  #### 2. Cache Configuration
  Cache the current configuration to enhance performance:
  ```bash
  php artisan config:cache
  ```

  #### 3.  Run Migrations and Migrate Seeder :
  Run migrations to set up or update the database schema and Migrate Seeder :
  ```bash
  php artisan migrate --seed
  ```
### Installing Required Packages: 
  #### 1. Install JWT Authentication Package:
  Install and configure the JWT Auth package for API token management:
  ```bash
  composer require php-open-source-saver/jwt-auth
php artisan vendor:publish --provider="PHPOpenSourceSaver\JWTAuth\Providers\LaravelServiceProvider"
php artisan jwt:secret
  ```
  #### 2. Install Spatie Laravel Permission Package:
  Install and configure the Spatie Laravel Permission package for role and permission management:
  ```bash
  composer require spatie/laravel-permission
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
php artisan migrate
  ```
  ### 3. Set Up Firebase Authentication

Follow the steps below to set up Firebase and integrate it with the project:

1. **Create a Firebase Account**  
   First, create an account on [Firebase](https://firebase.google.com/).

2. **Create a New Project**  
   After logging into Firebase, create a new project.

3. **Navigate to Project Settings**  
   Go to the **Project Settings** by clicking on the gear icon next to **Project Overview**.

4. **Save the Project ID**  
   Under the **General** tab, note down the **Project ID** as we will use this later in the `.env` file.

5. **Create a Service Account**  
   Go to the **Service Accounts** tab and click on **Generate New Private Key**.  
   A `.json` file will be downloaded, save it in the `storage/app/` directory of your project.

6. **Install Firebase Authentication Package**  
   Install the Google Auth package for Firebase integration by running:
   ```bash
   composer require google/auth
7. **Configure Firebase Credentials in config/services.php**  
   In the config/services.php file, update the credentialsPath with the path to the JSON file you downloaded:
   ```bash
   'credentialsPath' => storage_path('app/education-system-fc905-firebase-adminsdk-u2f1n-a7a06ef0e6.json')
8. **Update .env File**
  Add the Firebase Project ID to the .env file:
  ```bash
  FCM_PROJECT_ID=your_firebase_project_id
  ```
  Replace your_firebase_project_id with the Project ID you saved earlier.
### 4. Set Up VirusTotal API

Follow these steps to create an account on VirusTotal and integrate the API with your project:

1. **Create a VirusTotal Account**  
   Go to [VirusTotal](https://www.virustotal.com/) and create a new account or log in if you already have one.

2. **Get the API Key**  
   After logging in, navigate to your **Profile Settings** by clicking on your username in the top-right corner of the dashboard.  
   Under the **API Key** section, you will find your **API Key**.

3. **Store the API Key in `.env`**  
   Copy the **API Key** and add it to your `.env` file as follows:
   ```env
   VIRUSTOTAL_API_KEY=your_virustotal_api_key
  Replace your_virustotal_api_key with the VIRUSTOTAL_API_KEY in the [VirusTotal](https://www.virustotal.com/).
## doc of postman is
https://documenter.getpostman.com/view/34555205/2sAYBa8omR





