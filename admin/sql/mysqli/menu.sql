-- Menus
	
	-- get all menus 

	CREATE PROCEDURE getMenusList(

		-- variables
		IN  language_id INT(11),
		IN  menu_id INT(11),
		IN  site_id INT(11),
		IN  post_id INT(11),
		IN  search CHAR,
		IN  type CHAR,
		
		-- pagination
		IN start INT(11),
		IN limit INT(11),
			
		-- return array of menus for menus query
		OUT fetch_all,
		-- return menus count for count query
		OUT fetch_one,
	)
	BEGIN

		SELECT  *, menus.menu_id as array_key
			
			FROM menu AS menus
			
		
		LIMIT :start, :limit;
		
		SELECT count(*) FROM (
			
			@SQL_COUNT(menus.menu_id, menu) -- this takes previous query removes limit and replaces select columns with parameter menu_id
			
		) as count;


	END-- get all menus 	
	
	
	-- get all categories 

	CREATE PROCEDURE getMenus(

		-- variables
		IN  language_id INT(11),
		IN  menu_id INT(11),
		IN  site_id INT(11),
		IN  post_id INT(11),
		IN  search CHAR,
		IN  type CHAR,
		
		-- pagination
		IN start INT(11),
		IN limit INT(11),
			
		-- return array of menus for menus query
		OUT fetch_all,
		-- return menus count for count query
		OUT fetch_one,
	)
	BEGIN

		SELECT td.*,menus.url, menus.sort_order, menus.parent_id, menus.menu_item_id as array_key
			
		
			FROM menu_item AS menus
		
			-- INNER JOIN menu_to_site c2s ON (menus.menu_id = c2s.menu_id AND c2s.site_id = :site_id) 
			INNER JOIN menu_item_description td ON (menus.menu_item_id = td.menu_item_id AND td.language_id = :language_id)  
			
			WHERE 
			
				td.language_id = :language_id -- AND c2s.site_id = :site_id
	
			
			@IF isset(:menu_id)
			THEN 
			
				AND menus.menu_id = :menu_id
				
			END @IF			

		ORDER BY menus.parent_id, menus.sort_order, menus.menu_id
		LIMIT :start, :limit;
		
		SELECT count(*) FROM (
			
			@SQL_COUNT(menus.menu_id, menu) -- this takes previous query removes limit and replaces select columns with parameter menu_id
			
		) as count;


	END-- get all menus 	
	
	CREATE PROCEDURE getMenuAllLanguages(

		-- variables
		IN  language_id INT(11),
		IN  user_group_id INT(11),
		IN  site_id INT(11),
		IN  menu_id INT(11),
		IN  search CHAR,
		
		-- pagination
		IN start INT(11),
		IN limit INT(11),
			
		-- return array of categories for categories query
		OUT fetch_all,
		-- return categories count for count query
		OUT fetch_one,
	)
	BEGIN

		SELECT *, 
			(
				SELECT 
					CONCAT('[', GROUP_CONCAT(
					'{"language_id":"', cd.language_id, 
						'","name":"' , cd.name, 
						'","slug":"' , cd.slug, 
						'","description":"' , cd.description, 
						'"}'), ']') 
						
					FROM menu_item_description as cd 
				WHERE 
					cd.menu_item_id = categories.menu_item_id GROUP BY cd.menu_item_id
			) as languages
			
		FROM menu_item AS categories
		
			WHERE 1
					
	
			@IF isset(:search)
			THEN 
			
				AND td.name LIKE :search
				
			END @IF						
			
			@IF isset(:menu_id)
			THEN 
			
				AND categories.menu_id = :menu_id
				
			END @IF			

		ORDER BY categories.parent_id, categories.sort_order, categories.menu_item_id
		
		@IF isset(:limit)
		THEN 		
			LIMIT :start, :limit
		END @IF
		
		;
		
		SELECT count(*) FROM (
			
			@SQL_COUNT(categories.menu_item_id, menu) -- this takes previous query removes limit and replaces select columns with parameter product_id
			
		) as count;


	END
	


	-- Edit menu

	CREATE PROCEDURE editMenuItem(
		IN menu_item ARRAY,
		IN  menu_item_id INT(11),
		OUT insert_id
	)
	BEGIN

		-- allow only table fields and set defaults for missing values
		:menu_item_description_data = @FILTER(:menu_item.menu_item_description, menu_item_description);

		@EACH(:menu_item_description_data) 
			INSERT INTO menu_item_description 
		
				( @KEYS(:each), menu_item_id)
			
			VALUES ( :each, :menu_item_id)
				ON DUPLICATE KEY UPDATE @LIST(:each);

		-- allow only table fields and set defaults for missing values
		@FILTER(:menu_item, menu_item);
		
		UPDATE menu_item 
			
			SET @LIST(:menu_item) 
			
		WHERE menu_item_id = :menu_item_id;
	END	



	-- Add new menu

	CREATE PROCEDURE addMenuItem(
		IN menu_item ARRAY,
		OUT insert_id
	)
	BEGIN
		
		-- allow only table fields and set defaults for missing values
		:menu_item_description_data = @FILTER(:menu_item.menu_item_description, menu_item_description);
		:menu_item_data  = @FILTER(:menu_item, menu_item);

		-- INSERT INTO menu SET model = 'test 2', sku = '', upc = '', ean = '', jan = '', isbn = '', mpn = '', location = '', quantity = '1', minimum = '1', subtract = '1', stock_status_id = '6', date_available = '2017-05-14', manufacturer_id = '0', shipping = '1', price = '0', points = '0', weight = '0', weight_class_id = '1', length = '0', width = '0', height = '0', length_class_id = '1', status = '1', tax_class_id = '0', sort_order = '1', date_added = NOW()
		
		INSERT INTO menu_item 
		
			( @KEYS(:menu_item_data) )
			
		VALUES ( :menu_item_data );
			
		
		@EACH(:menu_item_description_data) 
			INSERT INTO menu_item_description 
		
				( `menu_item_id`, @KEYS(:each) )
			
			VALUES ( @result.menu_item, :each );
			
	 
        SELECT @menu_item as menu_item;
	END

	-- Reorder menu items

	CREATE PROCEDURE updateMenuItems(
		IN menu_items ARRAY,
		OUT insert_id
	)
	BEGIN
		
		:menu_item_data  = @FILTER(:menu_items, menu_item);
		
		@EACH(:menu_item_data) 
			UPDATE menu_item
			
				SET @LIST(:each) 
			
			WHERE menu_item_id = :each.menu_item_id;
		
	END	
	
	-- Delete menu item

	CREATE PROCEDURE deleteMenuItem(
		IN menu_item_id INT,
		OUT insert_id
	)
	BEGIN
	
		-- delete menu_item_description
		DELETE FROM `menu_item_description` WHERE menu_item_id IN (
		WITH RECURSIVE tree AS ( 
				   SELECT menu_item_id, 
					  parent_id
				   FROM menu_item
				   WHERE menu_item_id = :menu_item_id

				   UNION ALL 

				   SELECT p.menu_item_id,
						  p.parent_id 
				   FROM menu_item p
					 JOIN tree t ON t.menu_item_id = p.parent_id
				)
		SELECT menu_item_id FROM tree);
		
		-- delete menu_item
		DELETE FROM `menu_item` WHERE menu_item_id IN (
		WITH RECURSIVE tree AS ( 
				   SELECT menu_item_id, 
					  parent_id
				   FROM menu_item
				   WHERE menu_item_id = :menu_item_id

				   UNION ALL 

				   SELECT p.menu_item_id,
						  p.parent_id 
				   FROM menu_item p
					 JOIN tree t ON t.menu_item_id = p.parent_id
				)
		SELECT menu_item_id FROM tree);
		
	END