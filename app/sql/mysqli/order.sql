-- Orders

	-- get all orders

	CREATE PROCEDURE getOrders(
		IN customer_id INT,
	)
	BEGIN
        
        SELECT * FROM orders AS orders WHERE o.customer_id = :customer_id;
        
    END
    
	CREATE PROCEDURE getOrder(
		IN customer_id INT,
        IN order_id INT,
	)
	BEGIN
        
        SELECT * FROM orders AS `order` WHERE o.customer_id = :customer_id AND o.order_id = :order_id;
        
        	
		SELECT `key` as array_key,`value` as array_value FROM order_meta as _
			WHERE _.order_id = :order_id
            
	
		SELECT `product_id` as array_key FROM order_product as products
			WHERE products.order_id = :order_id
        
    END    
    
	-- add new order

	CREATE PROCEDURE addOrder(
		IN order ARRAY,
		OUT insert_id,
	)
	BEGIN

		-- insert order
		:products  = @FILTER(:order.products, order_product, false, true)
		@FILTER(:order, order, true, false)

		
		INSERT INTO `order`
			
			( @KEYS(:order) )
			
	  	VALUES ( :order );

		-- insert order products
		@EACH(:products) 
			INSERT INTO order_product 
				( `order_id`, `reward`, @KEYS(:each) )
			VALUES ( @result.order, 0,  :each  )

    END
	
	
	