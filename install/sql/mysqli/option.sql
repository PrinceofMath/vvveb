-- Options

	-- get one option

	CREATE PROCEDURE getOption(
		IN key CHAR,
		IN site_id INT,
		
		OUT fetch_one,
	)
	BEGIN

		SELECT value
            FROM `option` AS _
		WHERE _.`key` = :key;
		
	END
    
	CREATE PROCEDURE setOption(
		IN key CHAR,
		IN value CHAR,
        IN site_id INT,
		
		OUT insert_id
	)
	BEGIN

        INSERT INTO `option`
            (`key`, `value`, `site_id`)
        
        VALUES ( :key, :value, :site_id )
        
        ON DUPLICATE KEY 
            UPDATE  `value` = values(`value`);
		
	END
    
	CREATE PROCEDURE deleteOption(
		IN key CHAR,
		IN value INT,
	)
	BEGIN

        DELETE FROM 
            `option` 
        WHERE `key` = :key;
		
	END

    CREATE PROCEDURE getOptions(
		IN keys ARRAY,
		IN site_id INT,
	)
	BEGIN

		SELECT value
            FROM `option` AS o
		
		@IF !empty(:keys) 
		THEN 
			WHERE o.`key` IN (:keys)
		END @IF
		
	END    
    
    
	CREATE PROCEDURE setOptions(
		IN options ARRAY,
		IN site_id INT,
	)
	BEGIN

        INSERT INTO `option`
            (`key`, `value`, `site_id`)
        
		--@EACH(:options) 
			VALUES ( :each, :site_id)
		--END @EACH	
		
        -- @VALUES(:options) --@VALUES expands the array to the following expression
        --    ( :options.each.key, :options.each.value, :site_id )
        
        ON DUPLICATE KEY 
            UPDATE  `value` = VALUES(value);
		
	END
    
	CREATE PROCEDURE deleteOptions(
		IN keys ARRAY,
		IN site_id,
	)
	BEGIN

        DELETE FROM 
            `option` 
        WHERE `key` IN (:keys) AND site_id = :site_id;
		
	END    