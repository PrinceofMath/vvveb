-- Categories

	-- get one manufacturer



	CREATE PROCEDURE getManufacturer(
		IN manufacturer_id INT,
		OUT fetch_row, 
	)
	BEGIN
		-- manufacturer
		SELECT *
			FROM manufacturer as _ -- (underscore) _ means that data will be kept in main array
		WHERE manufacturer_id = :manufacturer_id LIMIT 1;

		 --SELECT *,manufacturer_option_id as _ 
			--FROM manufacturer_option  WHERE manufacturer_id = :manufacturer_id;
			--@EACH(manufacturer_option, manufacturer_option_value) 
				--SELECT *, manufacturer_option_value_id as _ FROM manufacturer_option_value pov 
					--WHERE manufacturer_option_id = :manufacturer_option[manufacturer_option_id];

	END
	



	-- Edit manufacturer

	CREATE PROCEDURE editManufacturer(
		IN manufacturer_array ARRAY,
		IN  manufacturer_id INT(11),
		OUT insert_id
	)
	BEGIN

		--SELECT * FROM manufacturer_option WHERE manufacturer_id = :manufacturer_id;

		-- allow only table fields and set defaults for missing values
		@FILTER(:manufacturer_array, manufacturer);
		
		UPDATE manufacturer 
			
			SET @LIST(:manufacturer_array) 
			
		WHERE manufacturer_id = :manufacturer_id
	END	



-- Add new manufacturer

	CREATE PROCEDURE addManufacturer(
		IN manufacturer_data ARRAY,
		OUT fetch_one
	)
	BEGIN
		
		-- allow only table fields and set defaults for missing values
		:manufacturer  = @FILTER(:manufacturer_data, manufacturer);
		:manufacturer_description = @FILTER(:manufacturer_data, manufacturer_description);

		-- INSERT INTO manufacturer SET model = 'test 2', sku = '', upc = '', ean = '', jan = '', isbn = '', mpn = '', location = '', quantity = '1', minimum = '1', subtract = '1', stock_status_id = '6', date_available = '2017-05-14', manufacturer_id = '0', shipping = '1', price = '0', points = '0', weight = '0', weight_class_id = '1', length = '0', width = '0', height = '0', length_class_id = '1', status = '1', tax_class_id = '0', sort_order = '1', date_added = NOW()
		
		INSERT INTO manufacturer 
		
			( @KEYS(:manufacturer) )
			
		VALUES ( :manufacturer );
			

		-- SET :manufacturer_description.manufacturer_id = last_insert_id;
        SET @manufacturer_id = LAST_INSERT_ID();

		-- UPDATE manufacturer SET image = :image WHERE manufacturer_id = :manufacturer_id;
		
		-- :manufacturer  = @FILTER(:manufacturer_data, manufacturer);
		
		INSERT INTO manufacturer_description 
		
			( `manufacturer_id`, @KEYS(:manufacturer_description) )
			
		VALUES ( @manufacturer_id, @LIST(:manufacturer_description) );
	 
        SELECT @manufacturer_id as manufacturer_id;
	END


	-- get all manufacturers 

	CREATE PROCEDURE getManufacturers(

		-- variables
		IN  language_id INT(11),
		IN  user_group_id INT(11),
		IN  site_id INT(11),
		IN search CHAR,
		
		-- pagination
		IN  start INT(11),
		IN count INT(11),
			
		-- return array of manufacturers for manufacturers query
		OUT fetch_all,
		-- return manufacturers count for count query
		OUT fetch_one,
	)
	BEGIN

		SELECT * FROM manufacturer AS manufacturers
		
			LEFT JOIN manufacturer_to_site p2s ON (manufacturers.manufacturer_id = p2s.manufacturer_id) 
			WHERE p2s.site_id = :site_id

			@IF isset(:search)
			THEN 
			
				AND name LIKE :search
				
			END @IF			

			
		LIMIT :start, :count;
		
		SELECT count(*) FROM (
			
			@SQL_COUNT(manufacturers.manufacturer_id, manufacturer) -- this takes previous query removes limit and replaces select columns with parameter product_id
			
		) as count;


	END


