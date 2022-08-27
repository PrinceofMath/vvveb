-- Orders

	-- get all account orders

	CREATE PROCEDURE getOrders(
		-- variables
		IN  language_id INT(11),
		IN  site_id INT(11),
		IN 	customer_id INT
		
		IN order_status CHAR,
		
		-- pagination
		IN start INT(11),
		IN limit INT(11),
		
		
		-- return
		OUT fetch_all, -- orders
		OUT fetch_one  -- count
	)
	BEGIN
        
        SELECT orders.*,os.name as order_status FROM `order` AS orders 
		
			LEFT JOIN order_status AS os ON (orders.order_status_id = os.order_status_id AND os.language_id = :language_id) 
			
		WHERE 1 
		
			AND orders.site_id = :site_id
			
			@IF isset(:customer_id)
			THEN 
				AND orders.customer_id = :customer_id
			END @IF
			
			@IF isset(:order_status)
			THEN 
				AND os.name = :order_status
			END @IF		


		LIMIT :start, :limit;
		
		SELECT count(*) FROM (
			
			@SQL_COUNT(order_id, order) -- this takes previous query removes limit and replaces select columns with parameter order_id
			
		) as count;

    END	
	
	
	
    
	CREATE PROCEDURE getOrder(
		IN customer_id INT,
        IN order_id INT,
		OUT fetch_row,
		OUT fetch_all,
		OUT fetch_all,
	)
	BEGIN
        
        SELECT * FROM `order` WHERE  1
			
		@IF isset(:customer_id)
		THEN 
			AND `order``.customer_id = :customer_id
		END @IF

		AND `order`.order_id = :order_id;
		
        	
		SELECT `key` as array_key,`value` as array_value FROM order_meta as _
			WHERE _.order_id = :order_id;
            
	
		SELECT `product_id` as array_key FROM order_product as products
			WHERE products.order_id = :order_id;
        
        
    END    
    
	-- get all account orders

	CREATE PROCEDURE placeOrder(
		IN order ARRAY,
	)
	BEGIN

		@FILTER(:order, order)
		
		INSERT INTO `order` 
			
			( @KEYS(:order) )
			
	  	VALUES ( :order )

		
		--@EACH(:order.products) 
			--INSERT INTO order_product 
		
				--( @KEYS(:each), order_id, meta_title, meta_description, meta_keyword )
			
			--VALUES ( :each, @result.order, '', '', '' );
    END
    
        
