-- Delete the database.
--
DROP DATABASE IF EXISTS `playsms`;

-- Delete the user.  Because it may be possible
-- that the user doesn't exist, we run the (small)
-- chance of an error here.  Do a grant on the user
-- first so that its created if it doesn't exist, then
-- delete it.  (MySql doesn't have an 'if exists' for users like
-- it does for tables, which requires us to do weird gyrations
-- like this.)
--
GRANT ALL PRIVILEGES ON `playsms%`.* TO 'playsms'@'localhost';
DROP USER 'playsms'@'localhost';

