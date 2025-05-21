# BukSU COT: Capstone Repository  
A web-based thesis submission and management platform designed for Bukidnon State University College of Technology (BukSU COT), with user authentication, including Google OAuth integration.

## Features

- User authentication (email/password and Google OAuth)  
- Thesis submission and file upload system  
- Admin approval and rejection system with email notifications  
- Activity logging for users and admins  
- Public access to approved thesis repository  
- User profile management  
- Admin dashboard for submission oversight

## Requirements

- PHP 7.4 or higher  
- MySQL 5.7 or higher  
- Composer  
- XAMPP (or any local development environment)

## Installation

1. Clone the repository:
    ```bash
    git clone https://github.com/cooliit/buksu-thesisrealm.git  
    cd buksu-thesisrealm
    ```

2. Install dependencies:
    ```bash
    composer install
    ```

3. Create a `.env` file in the root directory with the following variables:
    ```dotenv
    DB_HOST=localhost  
    DB_NAME=thesis_db  
    DB_USER=your_database_user  
    DB_PASS=your_database_password  
    APP_NAME=ThesisRealm  
    EMAIL_ADMIN=buksu.thesisrealm@gmail.com  
    EMAIL_PASS=your_gmail_app_password  
    GOOGLE_CLIENT_ID=your_google_client_id  
    GOOGLE_CLIENT_SECRET=your_google_client_secret  
    GOOGLE_REDIRECT_URI=http://localhost/buksu-thesisrealm/google-auth/google-callback.php
    ```

4. Import the database schema:
    ```bash
    mysql -u your_database_user -p your_database_name < import.sql
    ```

5. Configure Google OAuth:

    - Go to the [Google Cloud Console](https://console.cloud.google.com/)
    - Create a new project or select an existing one
    - Enable the **Google+ API**
    - Create OAuth 2.0 credentials
    - Set the redirect URI to:  
      `http://localhost/buksu-thesisrealm/google-auth/google-callback.php`

## Usage

1. Start your local server (XAMPP, etc.)
2. Open a browser and navigate to:  
   `http://localhost/buksu-thesisrealm/`
3. Register a new account or log in using your Google account

## Project Structure

- `assets/` – Images, JavaScript, and CSS files  
- `admin/` – Admin dashboard and submission management  
- `includes/` – Database connection and helper functions  
- `google-auth/` – Google OAuth integration  
- `mail/` – Email notification logic  
- `views/` – Main application pages  
- `styles/` – Custom CSS files  
- `vendor/` – Composer dependencies

## License

MIT License

## Contact

For questions or issues, contact:  
**cooliit13@gmail.com**
