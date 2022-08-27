-- Customers

	-- get all account customers

	CREATE PROCEDURE getCustomers(
		-- variables
		IN  language_id INT(11),
		IN  site_id INT(11),
		IN 	customer_id INT

		-- pagination
		IN  start INT(11),
		IN count INT(11),
		
		
		-- return
		OUT fetch_all, -- orders
		OUT fetch_one  -- count
	)
	BEGIN
        
        SELECT * FROM customer AS customers 
		
			
		WHERE 1 
		
		@IF isset(:customer_id)
		THEN 
			AND o.customer_id = :customer_id
		END @IF
		;
		
		SELECT count(*) FROM (
			
			@SQL_COUNT(customer_id, customer) -- this takes previous query removes limit and replaces select columns with parameter order_id
			
		) as count;		
        
    END
	
    
	CREATE PROCEDURE getCustomer(
		IN customer_id INT,
        IN customer_id INT,
	)
	BEGIN
        
        SELECT * FROM customer AS `customer` WHERE o.customer_id = :customer_id AND o.customer_id = :customer_id;
        
        	
		SELECT `key` as array_key,`value` as array_value FROM customer_meta as _
			WHERE _.customer_id = :customer_id
            
	
		SELECT `product_id` as array_key FROM customer_product as products
			WHERE products.customer_id = :customer_id
        
        
    END    
    
	-- get all account customers

	CREATE PROCEDURE placeCustomer(
		IN customer ARRAY,
	)
	BEGIN

		@FILTER(:customer, customer)
		
		INSERT INTO `customer` 
			
			( @KEYS(:customer) )
			
	  	VALUES ( :customer )

		
		--@EACH(:customer.products) 
			--INSERT INTO customer_product 
		
				--( @KEYS(:each), customer_id, meta_title, meta_description, meta_keyword )
			
			--VALUES ( :each, @result.customer, '', '', '' );
    END
    
        
