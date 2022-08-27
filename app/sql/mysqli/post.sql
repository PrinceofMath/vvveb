-- Posts

	-- get all post 

	CREATE PROCEDURE getPosts(
		IN start INT,
		IN limit INT,
		IN type CHAR,
		IN search CHAR,
		IN taxonomy_item_slug CHAR,
		IN post_id ARRAY,
		IN site_id INT,
		IN taxonomy_item_id INT,
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
			
			@IF isset(:taxonomy_item_id) || isset(:taxonomy_item_slug)
			THEN
				LEFT JOIN post_to_taxonomy_item pt ON (posts.post_id = pt.post_id)   
			END @IF			
        
        WHERE 1    
			
		-- type	
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
			
			
			@IF isset(:taxonomy_item_id)
			THEN
				AND pt.taxonomy_item_id = :taxonomy_item_id
			END @IF	
			
			
			@IF isset(:taxonomy_item_slug)
			THEN
				AND pt.taxonomy_item_id = (SELECT taxonomy_item_id FROM taxonomy_item_description WHERE slug = :taxonomy_item_slug LIMIT 1)
			END @IF

			-- order by
			@IF isset(:order_by)
			THEN
				ORDER BY $order_by $direction		
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
			LEFT JOIN post_description pd ON (_.post_id = pd.post_id AND pd.language_id = :language_id)  
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
		
		
		SELECT `key` as array_key,`value` as array_value FROM post_meta as _
			WHERE _.post_id = @result.post_id
		
          
	END

	-- Add new post

	CREATE PROCEDURE addPost(
		IN post ARRAY,
		OUT insert_id
	)
	BEGIN
		
		-- allow only table fields and set defaults for missing values
        :post_data = @FILTER(:post, post);
		
		INSERT INTO post 
			
			( @KEYS(:post_data) )
			
	  	VALUES ( :post_data )
       
	 
	END

	-- Edit post

	CREATE PROCEDURE editPost(
		IN post ARRAY,
		IN  id_post INT(11),
		OUT insert_id
	)
	BEGIN

        DELETE FROM post_description WHERE post_id = :post_id;
		
		@EACH(:post.post_description) 
			INSERT INTO post_description 
		
				( @KEYS(:each), post_id, meta_title, meta_description, meta_keyword )
			
			VALUES ( :each, :post_id, '', '', '' );


		--SELECT * FROM post_option WHERE post_id = :post_id;

		-- allow only table fields and set defaults for missing values
		@FILTER(:post, post);

		UPDATE post 
			
			SET  @LIST(:post) 
			
		WHERE post_id = :id_post
	 
	END
	
	-- Delete post

	CREATE PROCEDURE deletePost(
		IN  id_post INT(11),
	)
	BEGIN

		DELETE FROM post WHERE post_id = :id_post
	 
	END
	
	
	
	-- Get tags

	CREATE PROCEDURE postTags(
		IN  id_post INT(11),
	)
	BEGIN
	END
	
	
	-- Get categories

	CREATE PROCEDURE postCategories(
		IN  id_post INT(11),
	)
	BEGIN
	END


	-- comments 

	CREATE PROCEDURE getComments(
		IN start INT,
		IN limit INT,
	)
	BEGIN

		SELECT SQL_CALC_FOUND_ROWS *
		FROM comments AS comments
		LIMIT :start, :limit;
		
		//SELECT FOUND_ROWS() as count;
	 
	END
