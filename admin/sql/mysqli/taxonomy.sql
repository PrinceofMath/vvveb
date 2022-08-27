-- Taxonomy

	-- get all taxonomies

	CREATE PROCEDURE getTaxonomies(
		IN taxonomy_item_id INT,
		
		-- pagination
		IN start INT(11),
		IN limit INT(11),
		
		--filter
		IN post_type CHAR,

		OUT fetch_all 
	)
	BEGIN
		-- taxonomy_item
		SELECT *, taxonomy_id as array_key 
			FROM taxonomy as _ -- (underscore) _ means that data will be kept in main array
				-- LEFT JOIN taxonomy_to_site t2s ON (taxonomy_item.taxonomy_item_id = t2s.taxonomy_item_id) 
			
			WHERE 1
			
			@IF isset(:post_type)
			THEN 
			
				AND post_type = :post_type
				
			END @IF		

			@IF isset(:limit)
			THEN
				LIMIT :start, :limit
			END @IF;
		

	END
	

