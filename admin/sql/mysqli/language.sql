-- Languages

	-- get all languages

	PROCEDURE getAll(
		IN language_id INT,
		OUT fetch_row, 
	)
	BEGIN
		-- language
		SELECT *, language_id as array_key
			FROM language as _;
	END	
	
	-- get language

	PROCEDURE get(
		IN language_id INT,
		OUT fetch_row, 
	)
	BEGIN
		-- language
		SELECT *, language_id as array_key
			FROM language as _ WHERE language_id = language_id;
	END
	
	-- add language

	PROCEDURE add(
		IN language ARRAY,
		OUT insert_id
	)
	BEGIN
		
		-- allow only table fields and set defaults for missing values
		:language_data  = @FILTER(:language, language);
		
		
		INSERT INTO language 
			
			( @KEYS(:language_data) )
			
	  	VALUES ( :language_data );

	END
	
	-- edit language
	CREATE PROCEDURE editLanguage(
		IN language_array ARRAY,
		IN  language_id INT(11),
		OUT insert_id
	)
	BEGIN

		DELETE FROM language_description WHERE language_id = :language_id;
		
		@EACH(:language_array.language_description) 
			INSERT INTO language_description 
		
				( @KEYS(:each), language_id, excerpt)
			
			VALUES ( :each, :language_id, '');
		END @EACH		

		-- allow only table fields and set defaults for missing values
		@FILTER(:language_array, language)
		
		
		@IF !empty(:language_array) 
		THEN
			UPDATE language 
				
				SET @LIST(:language_array) 
				
			WHERE language_id = :language_id
		END @IF


	END
	