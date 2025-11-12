Fundi-Fix is a web application that connects residents with local artisans (**fundis**) for household services. The platform allows users to easily find, book, and review tradespeople such as plumbers, electricians, and technicians.
The goal of this project is to create a **secure and user-friendly platform** for both:
-Residents seeking household services, and  
-Fundis(artisans) looking for work opportunities.
- Secure **user registration and login** for residents and fundis.
-2Factor Authentication (2FA) via email verification codes.
- Users can create, view, and update their own profiles.
- Fundis can **create and manage public profiles** showing their skills, rates, and locations.
- Basic booking feature for residents to initiate service requests with fundis.

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

git clone [https://github.com/jimamuto/FundiFix-Project.git]

to do run migrations--- php migrations/migrations.php to create the tables 
run seeders-----  php migrations/seeders.php to populate the tables 

to test if your connection works 
run this command php test_connection

to test if the user has been registered
run this command php test_user.php

in order to  export reports 
composer require phpoffice/phpspreadsheet
composer require tecnickcom/tcpdf
