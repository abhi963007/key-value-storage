# ABHIRAM_PHP_1270
abhiram's php github repository

## Project Description
This project implements Google Authentication in a PHP web application. It allows users to securely sign in using their Google accounts, demonstrating modern authentication practices and secure user management.

## Features
- Google OAuth 2.0 Authentication
- Secure user session management
- User profile information display
- Responsive web interface
- Database integration for user data storage

## Technologies Used
- PHP 7.4+
- MySQL Database
- Google OAuth 2.0 API
- HTML5/CSS3
- JavaScript
- Bootstrap for responsive design
- Composer for dependency management

## Project Structure
```
Authentication with Google/
├── api/                    # PHP backend files and Google API integration
├── database.sql           # Database schema and initial setup
├── index.html            # Main application entry point
├── script.js             # Frontend JavaScript functionality
└── styles.css            # Custom styling
```

## Setup Instructions
1. Clone the repository
```bash
git clone https://github.com/HASHCOVET-TRAINEE/ABHIRAM_PHP_1270.git
```

2. Install dependencies using Composer
```bash
cd ABHIRAM_PHP_1270/Authentication\ with\ Google/api
composer install
```

3. Configure Database
- Import the `database.sql` file into your MySQL server
- Update database credentials in the configuration file

4. Google API Setup
- Create a project in Google Cloud Console
- Enable Google+ API
- Create OAuth 2.0 credentials
- Add authorized redirect URIs
- Update the client ID and client secret in the configuration

5. Run the Application
- Configure your web server (Apache/Nginx) to serve the project
- Access the application through your web browser

## Security Features
- Secure session handling
- OAuth 2.0 protocol implementation
- SQL injection prevention
- XSS protection
- CSRF token implementation

## Tasks Completed
- Google authentication completed
- Database integration
- Frontend implementation
- Security measures implementation

## Contributing
Contributions, issues, and feature requests are welcome. Feel free to check issues page if you want to contribute.

## License
This project is licensed under the MIT License - see the LICENSE file for details.
