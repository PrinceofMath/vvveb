-- Account

	-- check user information

	-- check user information

	CREATE PROCEDURE getList(
		IN start INT,
		IN limit INT,
        IN status INT,
		IN search CHAR,
		IN post_id ARRAY,
		
		-- return array of users for users query
		OUT fetch_all,
		-- return users count for count query
		OUT fetch_one,	)
	BEGIN
        
        SELECT * FROM user AS users WHERE 1 


            @IF isset(:status)
			THEN 
				AND users.status = :status 
        	END @IF	
			

            -- search
            @IF isset(:search)
			THEN 
				AND _usersuser LIKE CONCAT('%',:search,'%')
        	END @IF	     
            
			
			-- limit
			@IF isset(:limit)
			THEN
				LIMIT :start, :limit
			END @IF;		

		--SELECT FOUND_ROWS() as count;
		SELECT count(*) FROM (
			
			@SQL_COUNT(users.user_id, user) -- this takes previous query removes limit and replaces select columns with parameter user_id
			
		) as count;				
        
    END

	CREATE PROCEDURE get(
		IN user CHAR,
		IN email CHAR,
        IN user_id INT,
        IN status INT,
		OUT fetch_row,
	)
	BEGIN
        
        SELECT * FROM user AS _ WHERE 1 

            @IF isset(:user)
			THEN 
				AND _.user = :user 
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
		@FILTER(:user, user);
		
		INSERT INTO user 
			
			( @KEYS(:user) )
			
	  	VALUES ( :user );	 
	END    
    

	-- Edit comment

	CREATE PROCEDURE edit(
		IN user CHAR,
		IN email CHAR,
        IN user_id INT,
		IN user ARRAY,
		OUT affected_rows
	)
	BEGIN
		-- allow only table fields and set defaults for missing values
		@FILTER(:user, user);

		UPDATE user 
			
			SET @LIST(:user) 
			
		WHERE 

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
