
	-- get all tax classes as key => value pair

	CREATE PROCEDURE getStockStatuses(
		OUT fetch_row,
	)
	BEGIN
	
		SELECT 
		
			stock_status_id as array_key, -- tax_class_id as key
			name as array_value -- only set title as value and return  
			
		FROM stock_status AS _; -- return all rows directly without using tax_class as key in returning array
	
	END
