-- Account

	-- check login information

	CREATE PROCEDURE get(
		IN login CHAR,
		IN email CHAR,
        IN user_id INT,
        IN status INT,
		OUT fetch_row,
	)
	BEGIN
        
        SELECT * FROM user AS _ WHERE 1 

            @IF isset(:login)
			THEN 
				AND _.login = :login 
        	END @IF	

            @IF isset(:email)
			THEN 
				AND _.email = :email 
        	END @IF			


            @IF isset(:user_id)
			THEN 
				AND _.user_id = :user_id 
        	END @IF			

            @IF isset(:status)
			THEN 
				AND _.status = :status 
        	END @IF	
			
		LIMIT 1
        
    END
    
    

	-- Add new user

	CREATE PROCEDURE add(
		IN user ARRAY,
		OUT insert_id
	)
	BEGIN
		
		-- allow only table fields and set defaults for missing values
		@FILTER(:user, user)
		
		INSERT INTO user 
			
			( @KEYS(:user) )
			
	  	VALUES ( :user )	 
	END    
    

	-- Edit comment

	CREATE PROCEDURE edit(
		IN login CHAR,
		IN email CHAR,
        IN user_id INT,
		IN user ARRAY,
		OUT insert_id
	)
	BEGIN
		-- allow only table fields and set defaults for missing values
		@FILTER(:user, user);

		UPDATE user 
			
			SET @LIST(:user) 
			
		WHERE 

            @IF isset(:login)
			THEN 
				login = :login 
        	END @IF	

            @IF isset(:email)
			THEN 
				email = :email 
        	END @IF			

            @IF isset(:user_id)
			THEN 
				user_id = :user_id 
        	END @IF					
	END

-- Add new user

	CREATE PROCEDURE setRole(
        IN user_id INT,
        IN role CHAR,
        IN role_id INT
        OUT insert_id
	)
	BEGIN
		
	
		UPDATE user 
			
			SET  
            
            @IF isset(:role_id)
			THEN 
				role_id = :role_id 
        	END @IF		


            @IF isset(:role)
			THEN 
				role_id = (SELECT role_id FROM roles WHERE name = :role)
        	END @IF		

			
		WHERE user_id = :user_id 
    END
