-- Comments

	-- get all comment 

	CREATE PROCEDURE getComments(
		IN start INT,
		IN limit INT,
        IN post_id INT,
        IN user_id INT,
	)
	BEGIN

		SELECT SQL_CALC_FOUND_ROWS *
		FROM comment AS comments
			WHERE 1
            
            -- post
            @IF isset(:post_id)
			THEN 
				AND comments.post_id  = :post_id
        	END @IF	            
            
            -- user
            @IF isset(:user_id)
			THEN 
				AND comments.user_id  = :user_id
        	END @IF	            

		LIMIT :start, :limit;
		
		--SELECT FOUND_ROWS() as count_com;
	 
	END
	

	-- get one comment

	CREATE PROCEDURE getComment(
		IN comment_id INT(11),
		OUT fetch_row,
	)
	BEGIN

		SELECT * 
			FROM comment AS _
		WHERE 
			
			1

            @IF isset(:comment_id)
			THEN
                AND _.comment_id = :comment_id
        	END @IF			

        LIMIT 1; 
		
		
		-- SELECT `key` as array_key,`value` as array_value FROM comment_meta as _
			-- WHERE _.comment_id = @result.comment_id
		
          
	END

	-- Add new comment

	CREATE PROCEDURE addComment(
		IN comment ARRAY,
		OUT insert_id
	)
	BEGIN
		
		-- allow only table fields and set defaults for missing values
		@FILTER(:comment, comment)
		
		INSERT INTO comment 
			
			( @KEYS(:comment) )
			
	  	VALUES ( :comment )
        
	END

	-- Edit comment

	CREATE PROCEDURE editComment(
		IN comment ARRAY,
		IN  id_comment INT(11),
		OUT insert_id
	)
	BEGIN
		-- allow only table fields and set defaults for missing values
		@FILTER(:comment, comment);

		UPDATE comment 
			
			SET  @LIST(:comment) 
			
		WHERE comment_id = :id_comment
	 
	END
	
	-- Delete comment

	CREATE PROCEDURE deleteComment(
		IN  id_comment INT(11),
	)
	BEGIN

		DELETE FROM comment WHERE comment_id = :id_comment
	 
	END
