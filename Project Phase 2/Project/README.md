# Rule Lawyers – PHP Project (Phase 2)

This project implements a small board-game shop with public pages, an admin system, and a simple inventory manager using PHP and MySQL.

## Public Pages
- index.php  
- shop.php  
- product.php  
- about.php  
- contact.php  
- account.php (create admin account)

## Admin Features
Admin accounts are session-based. After logging in, the admin can:

- create a product  
- edit a product  
- delete a product  
- upload an image file for each product  
- log out  

Only logged-in admins can access admin pages.

## Database
The project uses the following tables:

- users  
- admins  
- categories  
- products  

All database access uses PDO with prepared statements.



## How to Run
1. Place the project inside the server’s public directory.  
2. Import the SQL tables into MySQL.  
3. Update database credentials in the configuration file.  
4. Create an admin account through `account.php`.  
5. Log in and manage products.  

