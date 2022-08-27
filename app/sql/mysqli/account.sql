-- Account

	-- check login information

	CREATE PROCEDURE login(
		IN email CHAR,
        IN password CHAR,
	)
	BEGIN
        
        SELECT * FROM customer AS a WHERE a.email = :email AND a.password = :password;
        
    END