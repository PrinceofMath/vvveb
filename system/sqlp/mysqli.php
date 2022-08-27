<?php

/**
 * Vvveb
 *
 * Copyright (C) 2022  Ziadin Givan
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 *
 */

return
[
	'FUNCTION_REGEX' => '/(CREATE\s+)?PROCEDURE\s+(?<name>\w+)\((?<params>.*?)\)\s+BEGIN\s+(?<statement>.*?)END(?!\s*@)/ms',

	'PARAM_REGEX' => '/(IN|OUT|INOUT|LOCAL)\s+([\.@\w]+)\s*(\w+)?(\(\d+\))?[,$\n\s]?/ms',

	'VALUES_REGEX' => '/\s*@VALUES\s*\(\s*:(?<list>[\w\[\]]+)\s*\)\s*/ms',

	'EACH_REGEX' => '/\s*@EACH\s*\(\s*(\w+)\s*\,\s*([\w\.]+)\s*\)\s*/ms',

	'EACH_VAR_REGEX' => '/\s*@EACH\s*\(\s*:(.+?)\s*\)\s*/ms',

	'FILTER_REGEX' => '/\s*:?(?<return>[\w_\.]+)?\s*=?\s*@FILTER\s*\(\s*:(?<data>[\w\._]+)\s*\,\s*(?<columns>[\w_]+),?\s*(?<addmissing>true|false)?,?\s*(?<array>true|false)?\s*\)\s*/ms',

	'VAR_REGEX' => '/:(\w+)/ms',

	'IMPORT_REGEX' => '/import\(([\w\-\_\.\/]+?)\);?/',

	//Generated model templates
	'MODEL_TEMPLATE' => '
namespace Vvveb\Sql%namespace%;

use \Vvveb\System\Db;

class %name%SQL
{

	private $db;
	
	public function __construct(){
		$this->db = Db::getInstance();
	}

	%methods%
}',

	'PARAMS_TEMPLATE' => '',

	'VARS_TEMPLATE' => <<<'PHP'
		if (isset($params['%name%']))
		$stmt->bindValue(':%name%', (%type%)$params['%name%'], PDO::PARAM_%type%);
PHP
	,

	'QUERY' => <<<'PHP'
		$prevSql = $sql ?? '';
		$sql = '%statement%';

		if ($sql) {
		$stmt['%query_id%'] = $this->db->execute($sql, $params, $paramTypes);
		
		$result = false;

		if ($stmt['%query_id%']) {
			if (method_exists($stmt['%query_id%'], 'get_result')) {
				$result = $stmt['%query_id%']->get_result();
			} else 	{
				$result = $this->db->get_result($stmt['%query_id%']);
			}
		}
		
		/*
		if ('%query_id%' == '_') {
			$value = %fetch%;
			if (is_array($value)) {
				$results = $results + $value;
			} else {
				$results = $value;
			}
		} else { */
		    if (!empty('%array_key%')) {
				if ($result)
				while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
					$values = $row;
					if (!empty('%array_value%')) {
						//$values = $row['%array_value%'];
						$values = $row['array_value'];
					} 
				
					if ('%query_id%' == '_') {
						//$results[$row['%array_key%']] = $values;
						$results[$row['array_key']] = $values;
					} else {
						//$results['%query_id%'][$row['%array_key%']] = $values;
						
						$results['%query_id%'][$row['array_key']] = $values;
						
					}
				}
			} else {
				if ('%query_id%' == '_') {
					$value = %fetch%;
					if (is_array($value)) {
						$results = $results + $value;
					} else {
						$results = $value;
					}
				} else  {
					$results['%query_id%'] = %fetch%;
				}
			}
		//}
	}
PHP
	,

	'EACH_QUERY' => '
		%statement%;
',

	'PLACEHOLDER' => '?',

	//'PGSQL_PLACEHOLDER' =>  '$%i%',

	'METHOD_MULTIPLE_TEMPLATE' => '
	function %name%($params = array())
	{
		//multiple
		$results = [];
        $stmt = [];
		$paramTypes = %param_types%;

		%statement%

		if ($results)
		return $results;
	}
',

	//macros
	'macros' => [
		'IFTHENELSE' => [
			'regex' => '/\s*@IF\s*(?<cond>.+?)\s*THEN\s*(?<then>.+?)\s*ELSE\s*(?<else>.+?)\s*END @IF?\s*/ms',
			'macro' => '\';
			
			if ($%cond) {
				$sql .= \' %then \';
			} else {
				$sql .= \' %else \';
			} 
			
			$sql .= \'', ],
		'IFTHEN' => [
			'regex' => '/\s*@IF\s*(?<cond>.+?)\s*THEN\s*(?<then>.+?)\s*END @IF?\s*/ms',
			'macro' => '\';
			
			if ($%cond) {
				$sql .= \' %then \';
			} 
			
			$sql .= \' 
			', ],
		'KEYS' => [
			'regex' => '/\s*@KEYS\s*\(\s*:(?<keys>[\w_\.]+)\s*\)\s*/ms',
			'macro' => '\';
			$sql .= \'`\' . implode(\'`,`\', array_keys($params[\'%keys\'])); 
			$sql .= \'` ', ],
		'LIST' => [
			'regex' => '/\s*@LIST\s*\(\s*:(?<list>[\w_\.]+)\s*\)\s*/ms',
			'macro' => '\';
		
			list($_sql, $_params) = $this->db->expandList($params[\'%list\'], \'%list\');

			$sql .= \' \' . $_sql;

			if (is_array($_params)) $paramTypes = array_merge($paramTypes, $_params);

			$sql .= \' \' . \'
			', ],
		'SQL_COUNT' => [
			'regex' => '/\s*@SQL_COUNT\s*\(\s*(?<column>.+?),\s*(?<table>.+?)\s*\)\s*/ms',
			'macro' => '\'; 
			$sql .= $this->db->sqlCount($prevSql, \'%column\', $this->db->prefix . \'%table\'); 
			$sql .= \'', ],
	],

	'tokenMap' => [
		'@IF+\s*.+?\s*THEN' => 'T_IF_START',
		'@ELSE'             => 'T_ELSE',
		'END @IF'           => 'T_IF_END',
		'@KEYS\(.+?\)'      => 'T_KEYS',
		'@LIST\(.+?\)'      => 'T_LIST',
		//'@EACH\(.+?\)'      => 'T_EACH_START',
		//'END @EACH'         => 'T_EACH_END',
		'@SQL_COUNT\(.+?\)' => 'T_SQL_COUNT',
		'.+?'               => 'T_SQL',
	],

	'macroMap' => [
		//if
		'T_IF_START' => [
			'/\s*@IF\s*(?<cond>.+?)\s*THEN\s*/', //regex
			<<<'PHP'
		';
		if ($%cond) {
			$sql .= '
PHP
			,
		],

		'T_ELSE' => <<<'PHP'
				';
			} else {
				$sql .= ' 		
PHP
		,
		'T_IF_END' => '\';
			} //end if
			
			$sql .= \'',
		//end if
		'T_KEYS'      => [
			'/\s*@KEYS\s*\(\s*:(?<keys>[\w_\.]+)\s*\)\s*/ms',
			<<<'PHP'
		';
		$sql .= '`' . implode('`,`', array_keys($params['%keys'])); 
		$sql .= '` 
PHP
		],
		'T_LIST'      => [
			'/\s*@LIST\s*\(\s*:(?<list>[\w_\.]+)\s*\)\s*/ms',
			<<<'PHP'
		';
		
			list($_sql, $_params) = $this->db->expandList($params['%list'], '%list');

			$sql .= ' ' . $_sql;

			if (is_array($_params)) $paramTypes = array_merge($paramTypes, $_params);

			$sql .= ' ' . '
PHP
		],
		'T_SQL_COUNT'      => [
			'/\s*@SQL_COUNT\s*\(\s*(?<column>.+?),\s*(?<table>.+?)\s*\)\s*/ms',
			<<<'PHP'
		'; 
		$sql .= $this->db->sqlCount($prevSql, '%column', $this->db->prefix . '%table'); 
		$sql .= '
PHP
		],
		//'T_EACH_START'      => 'EACH()',
		//'T_EACH_END'        => '}',
		'@SQL_COUNT\(.+?\)' => 'T_SQL_COUNT',
		'T_SQL'             => '',
	],
];
