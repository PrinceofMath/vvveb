-- Roles

	-- get all roles 

	CREATE PROCEDURE getRoles(
		IN start INT,
		IN rows INT,
	)
	BEGIN

		SELECT SQL_CALC_FOUND_ROWS *
            FROM roles AS roles
		LIMIT :start, :rows;
		
		-- SELECT FOUND_ROWS() as count;
	 
	END