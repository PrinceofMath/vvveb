-- Posts

	-- get all post 

	CREATE PROCEDURE getPosts(
		IN start INT,
		IN limit INT,
		IN type CHAR,
		IN search CHAR,
		IN post_id ARRAY,
		IN site_id INT,
		IN language_id INT,
		
		-- return array of posts for posts query
		OUT fetch_all,
		-- return posts count for count query
		OUT fetch_one,
	)
	BEGIN

		SELECT *
		FROM post AS posts
			LEFT JOIN post_description pd ON (posts.post_id = pd.post_id AND pd.language_id = :language_id)  
			LEFT JOIN post_to_site ps ON (posts.post_id = ps.post_id)  
			LEFT JOIN admin u ON (posts.user_id = u.admin_id)  
		WHERE 1 
		
			@IF isset(:type)
			THEN
				AND posts.type = :type
			END @IF


            -- search
            @IF isset(:search)
			THEN 
				AND pd.name LIKE CONCAT('%',:search,'%')
        	END @IF	     
            
            -- post_id
			@IF isset(:post_id) && count(:post_id) > 0
			THEN 
			
				AND posts.post_id IN (:post_id)
				
			END @IF		

			@IF isset(:site_id)
			THEN
				AND ps.site_id = :site_id
			END @IF

			-- limit
			@IF isset(:limit)
			THEN
				LIMIT :start, :limit
			END @IF;		

		--SELECT FOUND_ROWS() as count;
		SELECT count(*) FROM (
			
			@SQL_COUNT(posts.post_id, post) -- this takes previous query removes limit and replaces select columns with parameter product_id
			
		) as count;
	 
	END
	

	-- get one post

	CREATE PROCEDURE getPost(
		IN post_id INT(11),
        IN slug CHAR,
        IN language_id INT,
		OUT fetch_row,
	)
	BEGIN

		SELECT * 
			FROM post AS _
			LEFT JOIN post_description pd ON (_.post_id = pd.post_id)  
		WHERE 
			
			1

            @IF isset(:slug)
			THEN 
				AND pd.slug = :slug 
        	END @IF			

            @IF isset(:post_id)
			THEN
                AND _.post_id = :post_id
        	END @IF			

        LIMIT 1; 
		
		--description
		
		SELECT *, language_id as array_key -- (underscore) _ column means that this column (language_id) value will be used as array key when adding row to result array
			FROM post_description 
		WHERE post_id = @result.post_id;	 
	 
		-- meta
		
		SELECT `key` as array_key,`value` as array_value FROM post_meta as _
			WHERE _.post_id = @result.post_id	 
	 
	END

	-- Add new post

	CREATE PROCEDURE addPost(
		IN post ARRAY,
		IN post_id INT,
		IN site_id INT,
		OUT insert_id
	)
	BEGIN
		
		-- allow only table fields and set defaults for missing values
		:post_data  = @FILTER(:post, post);
		
		
		INSERT INTO post 
			
			( @KEYS(:post_data) )
			
	  	VALUES ( :post_data );

		:post.post_description  = @FILTER(:post.post_description, post_description, false, true);


		@EACH(:post.post_description) 
			INSERT INTO post_description 
		
				( @KEYS(:each), post_id)
			
			VALUES ( :each, @result.post);

		@EACH(:post.taxonomy_item) 
			INSERT INTO post_to_taxonomy_item 
		
				( `taxonomy_item_id`, post_id)
			
			VALUES ( :each, @result.post)
			ON DUPLICATE KEY UPDATE `taxonomy_item_id` = :each;

		INSERT INTO post_to_site 
		
			( `post_id`, `site_id` )
			
		VALUES ( @result.post, :site_id );			

	END

	-- Edit post

	CREATE PROCEDURE editPost(
		IN post ARRAY,
		IN post_id INT(11),
		OUT insert_id
	)
	BEGIN
		
		@EACH(:post.post_description) 
			INSERT INTO post_description 
		
				( @KEYS(:each), post_id, excerpt)
			
			VALUES ( :each, :post_id, '')
			ON DUPLICATE KEY UPDATE @LIST(:each);

		-- @IF !empty(:post.taxonomy_item) 

		DELETE FROM post_to_taxonomy_item WHERE post_id = :post_id;

		@EACH(:post.taxonomy_item) 
			INSERT INTO post_to_taxonomy_item 
		
				( `taxonomy_item_id`, post_id)
			
			VALUES ( :each, :post_id)
			ON DUPLICATE KEY UPDATE `taxonomy_item_id` = :each;

		-- END @IF

		-- allow only table fields and set defaults for missing values
		@FILTER(:post, post)
	
		@IF !empty(:post) 
		THEN
			UPDATE post 
				
				SET @LIST(:post) 
				
			WHERE post_id = :post_id
		END @IF


	END
	
	
	-- Delete post

	CREATE PROCEDURE deletePost(
		IN  post_id INT(11),
		IN  site_id INT(11),
		OUT affected_rows
	)
	BEGIN
		
		DELETE FROM post_to_taxonomy_item WHERE post_id = :post_id;
		DELETE FROM post_to_site WHERE post_id = :post_id;
		DELETE FROM post_description WHERE post_id = :post_id;
		DELETE FROM post WHERE post_id = :post_id;
	 
	END
	
	
	
	-- Get tags

	CREATE PROCEDURE postTags(
		IN  id_post INT(11),
		OUT affected_rows
	)
	BEGIN
	END
	
	
	-- Get categories

	CREATE PROCEDURE postCategories(
		IN  id_post INT(11),
		OUT affected_rows
	)
	BEGIN
	END
	
	-- Add categories
	CREATE PROCEDURE setPostTaxonomy(
		IN  id_post INT(11),
		IN  taxonomy_item ARRAY,
		OUT affected_rows
	)
	BEGIN
	
		DELETE FROM post_to_taxonomy_item WHERE post_id = :id_post;
	
		@EACH(:taxonomy_item) 
			INSERT IGNORE INTO post_to_taxonomy_item 
		
				( `post_id`, `taxonomy_item_id`)
			
			VALUES ( :post_id, :each);
	END