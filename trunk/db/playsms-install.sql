-- Create user and give it full rights on databases that start with its name.
--
-- Note that we don't do a 'create user' here because it will error 
-- out if the user already exists.  Plus, there's no 'if not exists' check
-- for users like on tables.  This is clearly retarded.  Fortunately,
-- granting a privilege will create the user if it doesn't exist and will
-- just change the privileges on an existing user, so we avoid the erroring.
-- Then we just set the password on the user.
--
GRANT ALL PRIVILEGES ON `playsms%`.* TO 'playsms'@'localhost';
SET PASSWORD FOR 'playsms'@'localhost' = password('playsms');

