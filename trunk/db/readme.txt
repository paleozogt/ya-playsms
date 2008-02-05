__Database Setup__

Always run this first...
        mysql < playsms-install.sql

If you are NOT upgrading, then run...
        mysql < playsms-makedb.sql

If you ARE upgrading...
    Run the playsms-upgrade_*.sql that corresponds to the version you're are
    upgrading TO (including all intermediate upgrades as they are not cumulative).
    e.g., if you are currently running v0.8.1 and are upgrading to v0.8.3, then do this:
        mysql < playsms-upgrade_0.8.2.sql
        mysql < playsms-upgrade_0.8.3.sql

If you are a developer...
    If you are making database structure changes, you need to do 3 things:

        1) Re-generate php code that uses the database:
                cd db ; generate-dbcode.php ../web/DataObjects/ 

        2) Update the playsms-makedb.sql to reflect your changes.
           DO NOT just use mysqldump, as the sql file has initial values for lots 
           of its tables.  Also, we don't want your database info in the default database.

        3) Create a playsms-upgrade_*.sql, which contains sql that will change the
           old database structure to the new one.  Use mysqldiff. Beware any AUTO_INCREMENT 
           changes-- generally you should remove these from the generated sql file.
           And make sure you test the upgrade!

