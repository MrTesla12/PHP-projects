-- SQL script to create the 'profiles' table 
DROP TABLE IF EXISTS profiles;

-- Table to store user profile information
CREATE TABLE profiles (
  id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  first_name      VARCHAR(100) NOT NULL, -- First name of the user
  last_name       VARCHAR(100) NOT NULL, -- Last name of the user
  email           VARCHAR(255) NOT NULL UNIQUE, -- Unique email for each profile
  bio             TEXT,
  image_path      VARCHAR(255) NOT NULL,     -- Path to the uploaded image
  image_mime      VARCHAR(100),              -- MIME type of the image
  image_size_bytes INT UNSIGNED,             -- Size of the image in bytes
  created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
