-- Products

	-- get one product

	PROCEDURE get(
		IN product_id INT,
		IN slug CHAR,
		IN language_id INT,
		OUT fetch_row, 
	)
	BEGIN
		-- product
		SELECT *
			FROM product as _ -- (underscore) _ means that data will be kept in main array
            LEFT JOIN product_description pd ON (_.product_id = pd.product_id)  
		WHERE 
        
			1

            @IF isset(:slug)
			THEN 
				AND pd.slug = :slug 
        	END @IF			

            @IF isset(:product_id)
			THEN 
                AND _.product_id = :product_id
        	END @IF		
        
        LIMIT 1;

		--description
		SELECT *,language_id, language_id as array_key -- (underscore) _ column means that this column (language_id) value will be used as array key when adding row to result array
			FROM product_description 
		WHERE product_id = :product_id;	 

		--images
		SELECT image, product_image_id as id, product_image_id as array_key --product_image_id will be used as key
			FROM product_image AS images
		WHERE product_id = @result.product_id ORDER BY sort_order;	 

		 --SELECT *,product_option_id as _ 
			--FROM product_option  WHERE product_id = :product_id;
			--@EACH(product_option, product_option_value) 
				--SELECT *, product_option_value_id as _ FROM product_option_value pov 
					--WHERE product_option_id = :product_option[product_option_id];

	END


	-- Edit product

	PROCEDURE edit(
		IN product ARRAY,
		IN  product_id INT(11),
		OUT insert_id
	)
	BEGIN

		DELETE FROM product_description WHERE product_id = :product_id;
		
		
		@EACH(:product.product_description) 
			INSERT INTO product_description 
		
				( @KEYS(:each), product_id, meta_title, meta_description, meta_keyword )
			
			VALUES ( :each, :product_id, '', '', '' );


		--SELECT * FROM product_option WHERE product_id = :product_id;

		-- allow only table fields and set defaults for missing values
		@FILTER(:product, product);
		
		UPDATE product 
			
			SET @LIST(:product) 
			
		WHERE product_id = :product_id
	END	


	-- Add product

	PROCEDURE addProduct(
		IN product ARRAY,
		OUT insert_id
	)
	BEGIN
    
		-- allow only table fields and set defaults for missing values
		:product_data  = @FILTER(:product, product)
		
		INSERT INTO product 
		
			( @KEYS(:product_data) )
			
		VALUES ( :product_data );

		:product_description = @FILTER(:product, product_description)

		INSERT INTO product_description 
		
			( `product_id`, @KEYS(:product_description) )
			
		VALUES ( @result.product, :product_description );    

	END	

	-- Get all products 

	PROCEDURE getAll(

		-- variables
		IN language_id INT(11),
		IN user_group_id INT(11),
		IN site_id INT(11),
		IN taxonomy_item_id INT(11),
		IN search CHAR,
		IN product_id ARRAY,
		IN order_by CHAR,
		IN direction CHAR,
		
		-- pagination
		IN  start INT(11),
		IN limit INT(11),
		
		-- columns options (local variables used for conditional sql)
		LOCAL include_manufacturer INT(11),
		LOCAL include_discount INT(11),
		LOCAL include_special INT(11),
		LOCAL include_reward INT(11),
		LOCAL include_stock_status INT(11),
		LOCAL include_image_gallery INT(11),
			
		-- return array of products for products query
		OUT fetch_all,
		-- return products count for count query
		OUT fetch_one,
	)
	BEGIN

		SELECT  --SQL_CALC_FOUND_ROWS *,
				*,
                pd.name AS name,
                products.image

				@IF !empty(:include_manufacturer) 
				THEN 
					,m.name AS manufacturer
				END @IF


			-- include image gallery 	
			@IF !empty(:include_image_gallery) 
			THEN 
				,(SELECT CONCAT('[', GROUP_CONCAT('{"id":"', pi.product_image_id, '","image":"' , pi.image, '"}'), ']') FROM product_image as pi WHERE pi.product_id = products.product_id GROUP BY pi.product_id) as images
			END @IF


			-- include discount 	
			@IF !empty(:include_discount) && !empty(:user_group_id) 
			THEN 
			
				 ,(SELECT price
				   FROM product_discount pd2
				   WHERE pd2.product_id = products.product_id
					 AND pd2.user_group_id = :user_group_id
					 AND pd2.quantity = '1'
					 AND ((pd2.date_start = '0000-00-00'
						   OR pd2.date_start < NOW())
						  AND (pd2.date_end = '0000-00-00'
							   OR pd2.date_end > NOW()))
				   ORDER BY pd2.priority ASC, pd2.price ASC
				   LIMIT 1) AS discount
				   
			END @IF
			
			-- include special price 	
			@IF !empty(:include_special) && !empty(:user_group_id) 
			THEN 
			
			  ,(SELECT price
			   FROM product_special ps
			   WHERE ps.product_id = products.product_id
				 AND ps.user_group_id = :user_group_id
				 AND ((ps.date_start = '0000-00-00'
					   OR ps.date_start < NOW())
					  AND (ps.date_end = '0000-00-00'
						   OR ps.date_end > NOW()))
			   ORDER BY ps.priority ASC, ps.price ASC
			   LIMIT 1) AS special
			   
			END @IF


			-- include reward 	
			@IF !empty(:include_reward) && !empty(:user_group_id) 
			THEN 
			
			  ,(SELECT points
			   FROM product_reward pr
			   WHERE pr.product_id = products.product_id
				 AND pr.user_group_id = :user_group_id
			   AS reward
			   
			END @IF
			
			-- include stock_status 	
			@IF !empty(:include_stock_status)
			THEN 

			  ,SELECT ss.name
			   FROM stock_status ss
			   WHERE ss.stock_status_id = products.stock_status_id
				 AND ss.language_id = :language_id) 
			  AS stock_status,

			   
			END @IF


			-- include weight_class 	
			@IF !empty(:include_weight_class)
			THEN 
			
			  ,(SELECT wcd.unit
			   FROM weight_class_description wcd
			   WHERE products.weight_class_id = wcd.weight_class_id
				 AND wcd.language_id = :language_id) 
			   AS weight_class
			   
			END @IF


			-- include length_class 	
			@IF !empty(:include_length_class)
			THEN 
			
			  ,(SELECT lcd.unit
			   FROM length_class_description lcd
			   WHERE products.length_class_id = lcd.length_class_id
				 AND lcd.language_id = :language_id) 
			   AS length_class
			   
			END @IF
		
		
			-- include rating
			@IF !empty(:include_rating)
			THEN 
			
			  ,(SELECT AVG(rating) AS total
			   FROM review r1
			   WHERE r1.product_id = products.product_id
				 AND r1.status = '1'
			   GROUP BY r1.product_id) 
			  AS rating

			   
			END @IF
		
			-- include reviews
			@IF !empty(:include_reviews)
			THEN 

			  ,(SELECT COUNT(*) AS total
			   FROM review r2
			   WHERE r2.product_id = products.product_id
				 AND r2.status = '1'
			   GROUP BY r2.product_id) AS reviews
									
			   
			END @IF

		 
		FROM product AS products
		
			LEFT JOIN product_to_site p2s ON (products.product_id = p2s.product_id) 
			LEFT JOIN product_description pd ON (products.product_id = pd.product_id)  

			@IF !empty(:include_manufacturer) 
			THEN 
				LEFT JOIN manufacturer m ON (products.manufacturer_id = m.manufacturer_id)
			END @IF		
			
			@IF !empty(:taxonomy_item_id) 
			THEN 
				INNER JOIN product_to_category pc ON (products.product_id = pc.product_id AND pc.taxonomy_item_id = :taxonomy_item_id)
			END @IF		
			
			
			WHERE pd.language_id = :language_id AND p2s.site_id = :site_id
			
            -- search
            @IF isset(:search)
			THEN 
				AND pd.name LIKE CONCAT('%',:search,'%')
        	END @IF	     
            
			@IF isset(:product_id) && count(:product_id) > 0
			THEN 
			
				AND products.product_id IN (:product_id)
				
			END @IF					
		
		-- ORDER BY parameters can't be binded because they are added to the query directly they must be properly sanitized by only allowing a predefined set of values
		@IF isset(:order_by)
		THEN
			ORDER BY $order_by $direction		
		END @IF					
			
		LIMIT :start, :limit;
		
		-- SELECT FOUND_ROWS() as limit;
		
		SELECT count(*) FROM (
			
			@SQL_COUNT(products.product_id, product) -- this takes previous query removes limit and replaces select columns with parameter product_id
			
		) as count;


	END