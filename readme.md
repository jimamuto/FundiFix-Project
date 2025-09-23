Fundi-Fix
Fundi-Fix is a web application that connects residents with local artisans (fundis) for household services. The platform allows users to find, book, and review tradespeople like plumbers and electricians.

Project Goal
The goal of this project is to create a secure and easy-to-use platform for both residents seeking services and artisans looking for work.

Features
This project currently includes the features required for Sprints 1 and 2.

User Authentication: Secure registration and login for residents and fundis.

2-Factor Authentication (2FA): Users verify their identity with a code sent to their email upon login.

User Management (CRUD): Users can create, view, and update their own profile information.

Fundi Profiles: Users with the 'Fundi' role can create and manage a public profile listing their skills and location.

Booking System: A basic system for residents to initiate a service request with a fundi.

Technology Used
Backend: PHP

Database: MariaDB 

Frontend: HTML5, Bootstrap 5

Server: Apache 

Dependencies: PHPMailer (for sending emails)

Project Structure
The application uses a Model-View-Controller (MVC) structure.

/config/: Holds the database and email configuration.

/public/: The web server root. index.php is the single entry point (router).

/app/models/: Contains the PHP classes that talk to the database.

/app/controllers/: Contains the main application logic.

/app/views/: Contains the HTML files that the user sees.

/vendor/: Contains third-party libraries installed via Composer.

How to Set Up
To run this project locally, follow these steps:

Prerequisites:

A local server like XAMPP or WAMP.

Composer.

Clone the Repository:

git clone [Your-GitHub-Repository-URL]
