
	-- get all tax classes as key => value pair

	CREATE PROCEDURE getTaxClasses(
		OUT fetch_row,
	)
	BEGIN
	
		SELECT 
		
			tax_class_id as array_key, -- tax_class_id as key
			title as array_value -- only set title as value and return  
			
		FROM tax_class AS _; -- return all rows directly without using tax_class as key in returning array
	
	END
