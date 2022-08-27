-- Account


	CREATE PROCEDURE getList(
		IN start INT,
		IN limit INT,
        IN status INT,
		IN search CHAR,
		IN post_id ARRAY,
		
		-- return array of admins for admins query
		OUT fetch_all,
		-- return admins count for count query
		OUT fetch_one,	)
	BEGIN
        
        SELECT * FROM admin AS admins WHERE 1 


            @IF isset(:status)
			THEN 
				AND admins.status = :status 
        	END @IF	
			

            -- search
            @IF isset(:search)
			THEN 
				AND _adminsuser LIKE CONCAT('%',:search,'%')
        	END @IF	     
            
			
			-- limit
			@IF isset(:limit)
			THEN
				LIMIT :start, :limit
			END @IF;		

		--SELECT FOUND_ROWS() as count;
		SELECT count(*) FROM (
			
			@SQL_COUNT(admins.user_id, user) -- this takes previous query removes limit and replaces select columns with parameter user_id
			
		) as count;				
        
    END

	-- check user information

	CREATE PROCEDURE get(
		IN user CHAR,
		IN email CHAR,
        IN admin_id INT,
        IN status INT,
		OUT fetch_row,
	)
	BEGIN
        
        SELECT * FROM admin AS _ WHERE 1 

            @IF isset(:user)
			THEN 
				AND _.user = :user 
        	END @IF	

            @IF isset(:email)
			THEN 
				AND _.email = :email 
        	END @IF			


            @IF isset(:admin_id)
			THEN 
				AND _.admin_id = :admin_id 
        	END @IF			

            @IF isset(:status)
			THEN 
				AND _.status = :status 
        	END @IF	
			
		LIMIT 1
        
    END
    
    

	-- Add new admin

	CREATE PROCEDURE add(
		IN admin ARRAY,
		OUT insert_id
	)
	BEGIN
		
		-- allow only table fields and set defaults for missing values
		@FILTER(:admin, admin)
		
		INSERT INTO admin 
			
			( @KEYS(:admin) )
			
	  	VALUES ( :admin )	 
	END    
    

	-- Update admin 
	
	CREATE PROCEDURE edit(
		IN email CHAR,
        IN admin_id INT,
		IN admin ARRAY,
		OUT affected_rows
	)
	BEGIN
		-- allow only table fields and set defaults for missing values
		@FILTER(:admin, admin);

		UPDATE admin 
			
			SET @LIST(:admin) 
			
		WHERE 

            @IF isset(:email)
			THEN 
				email = :email 
        	END @IF			

            @IF isset(:admin_id)
			THEN 
				admin_id = :admin_id 
        	END @IF					
	END

-- Add new admin

	CREATE PROCEDURE setRole(
        IN admin_id INT,
        IN role CHAR,
        IN role_id INT
        OUT insert_id
	)
	BEGIN
		
	
		UPDATE admin 
			
			SET  
            
            @IF isset(:role_id)
			THEN 
				role_id = :role_id 
        	END @IF		


            @IF isset(:role)
			THEN 
				role_id = (SELECT role_id FROM roles WHERE name = :role)
        	END @IF		

			
		WHERE admin_id = :admin_id 
    END
