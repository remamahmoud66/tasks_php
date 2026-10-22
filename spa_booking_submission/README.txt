SPA TREATMENT BOOKING SYSTEM - LOGIN AND REGISTER

Included files:
- register.php: creates a customer account
- login.php: logs in an existing customer
- welcome.php: simple page shown after successful login
- logout.php: ends the customer session
- db.php: connects PHP to MySQL using PDO
- style.css: styles the pages
- database.sql: creates the database and customers table

HOW TO RUN WITH XAMPP:
1. Copy the whole spa_booking_submission folder into: C:\xampp\htdocs\
2. Start Apache and MySQL from the XAMPP Control Panel.
3. Open http://localhost/phpmyadmin
4. Click Import, choose database.sql, and click Import/Go.
5. If your MySQL username/password are different, update db.php. The default XAMPP setup usually uses username root and an empty password.
6. Open http://localhost/spa_booking_submission/register.php
7. Create an account, then log in at http://localhost/spa_booking_submission/login.php

This submission covers the initial customer authentication part only. Appointment scheduling and haircut/beard service selection have not been implemented yet.
