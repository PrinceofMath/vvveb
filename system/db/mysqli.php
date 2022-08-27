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

namespace Vvveb\System\Db;

use Vvveb\System\Event;

//ini_set('mysql.trace_mode', '0');
//ini_set('mysqli.trace_mode', '0');

//define('SQL_ALLOW_FUNCTIONS','NOW,DATE,CURTIME');

define('SQL_VAR_REGEX',
	'/:([a-zA-Z0-9\[][\.\'a-zA-Z0-9\[\]_-]+)/ms');

class mysqli_result {
	private $stmt;

	private $meta;

	public function __construct($stmt) {
		$this->stmt = $stmt;
	}

	public function fetch_all() {
		return $this->fetch_assoc();
	}

	public function fetch_array($resulttype) {
		return $this->fetch_assoc();
	}

	public function fetch_assoc() {
		$meta = $this->stmt->resultMetadata();

		while ($field = $meta->fetchField()) {
			$params[] = &$row[$field->name];
		}

		call_user_func_array([$this->stmt, 'bind_result'], $params);

		while ($this->stmt->fetch()) {
			foreach ($row as $key => $val) {
				$c[$key] = $val;
			}
			$result[] = $c;
		}

		$this->stmt->close();

		return $result;
	}

	public function fetchField() {
	}

	public function fetchFields() {
	}

	public function fetchRow() {
		return $this->fetch_assoc();
	}
}

class Mysqli extends \Mysqli {
	private static $link;

	//public $error;

	private $stmt;

	public $prefix = 'vv_';

	public function get_result($stmt) {
		$result = new mysqli_result($stmt);

		return $result;
	}

	public function __construct($host = DB_HOST, $dbname = DB_NAME, $user = DB_USER, $pass = DB_PASS,  $prefix = DB_PREFIX) {
		//mysqli_report(MYSQLI_REPORT_OFF);
		//connect to database
		if (self :: $link) {
			return $link;
		}
		$this->prefix = $prefix;

		mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

		try {
			self :: $link = parent::__construct(/*'p:' . */$host, $user, $pass, $dbname);
			//self :: $link = $this;
		} catch (\mysqli_sql_exception $e) {
			$errorMessage = str_replace($pass,'*****', $e->getMessage());

			throw new \Exception($errorMessage, $e->getCode());
		}

		// check if a connection established
		if (\mysqli_connect_errno()) {
			$errorMessage = str_replace($pass,'*****',mysqli_connect_error());

			throw new \Exception($errorMessage, mysqli_connect_errno());
		}

		return self :: $link;
	}

	public function _filter($data, $columns, $addMissingDefaults = false) {
		//remove fields that are not table columns, $colums is returned by sqlp->getColumnsMeta()
		foreach ($data as $key => $name) {
			if (! isset($columns[$key])) {
				unset($data[$key]);
			}
		}

		if ($addMissingDefaults) {
			foreach ($columns as $name => $options) {
				if (isset($options['e']) && $options['e'] == 'auto_increment') {
					continue;
				}
				//todo: validate based on data type (t)
				//if there is no data for column and column is not nullable set default
				if (! isset($data[$name]) && $options['n'] == false) {
					$data[$name] = $options['d'];

					if ($options['d'] == NULL) {
						$data[$name] = '';
					}

					if ($options['t'] == 'int' || $options['t'] == 'decimal' || $options['t'] == 'tinyint') {
						$data[$name] = 1;
					}

					if ($options['t'] == 'datetime') {
						$data[$name] = date('Y-m-d H:i:s');
					}
				}
			}
		}

		return $data;
	}

	public function filter($data, $columns, $addMissingDefaults = false) {
		//check if collection of rows or individual row
		reset($data);

		if (is_numeric(key($data))) {
			//rows
			foreach ($data as $key => $row) {
				$return[$key] = $this->_filter($row, $columns, $addMissingDefaults);
			}

			return $return;
		} else {
			//row
			return $this->_filter($data, $columns, $addMissingDefaults);
		}
	}

	/*
	* Expands arrays a = ['param' => 'value', 'second' => value]; to a[param], a[second]
	*/
	public function expandArray($array, $arrayName) {
		$first      = true;
		$sql        = '';
		$parameters = [];

		if (is_array($array)) {
			foreach ($array as $key => $value) {
				if (! $first) {
					$sql .= ',';
				}
				$arrayKey = "['" . $arrayName . "']['" . $key . "']";
				$sql .= ':' . $arrayKey;
				$parameters[$arrayKey] = (is_int($value) ? 'i' : 's');
				$first                 = false;
			}
		}
		//var_dump(array($sql, $parameters));
		return [$sql, $parameters];
	}

	/**
	 * Generate a SQL list used for inserts 
	 * input ['var1' => 1, 'var2'=> 2, 'var3'=> 3] output var1 = 1, var2 = 2, var3 = 3.
	 * @param mixed $array 
	 * @param mixed $arrayName 
	 *
	 * @return mixed 
	 */
	public function expandList($array, $arrayName) {
		$first      = true;
		$sql        = '';
		$parameters = [];

		foreach ($array as $key => $value) {
			if (! $first) {
				$sql .= ',';
			}
			$arrayKey = "['$arrayName']['$key']";
			$sql .= "`$key` = :$arrayKey";
			$parameters[$arrayKey] = (is_int($value) ? 'i' : 's');
			$first                 = false;
		}

		return [$sql, $parameters];
	}

	public function sqlCount($query, $column, $table) {
		//remove limit
		$query = preg_replace('/LIMIT\s+(\d+|:\w+),\s*(\d+|:\w+)\s*;?$/', '', $query);

		$query = preg_replace("/^\s*SELECT .*?\s*FROM\s*$table /ms", "SELECT $column FROM $table ", $query);

		return $query;
	}

	/*
	 * Convert array dot notation to php notation 
	 * Ex: my.array.key to ['my']['array']['key']
	 */
	function sqlPhpArrayKey($key) {
		return '[\'' . str_replace('.', '\'][\'', $key) . '\']';
	}

	/*
	 * Replace :named_params with ?
	 */
	public function paramsToQmark(&$sql, &$params = [], &$paramTypes = []) {
		$parameters = [];
		$sql        = preg_replace_callback(
		SQL_VAR_REGEX,
		function ($matches) use (&$params, &$parameters, &$types, $paramTypes) {
			$varName = $matches[1];

			if (strpos($varName, '.') !== false) {
				$varName = $this->sqlPhpArrayKey($varName);
			}
			//if parameters is array element
			if ($varName[0] == '[') {
				if (preg_match_all('/[\w_-]+/', $varName, $arrayKeys)) {
					$type = $paramTypes[$varName] ?? 's';

					$types .= $type;

					$key1 = $arrayKeys[0][0];
					$key2 = $arrayKeys[0][1];

					$parameter = &$params[$key1][$key2];
					//if (strpos($parameter, ')') && )
					$parameters[] = $parameter;
				}
			} else {
				if (isset($params[$varName])) {
					$parameter = &$params[$varName];

					if (isset($paramTypes[$varName])) {
						$type = $paramTypes[$varName];
					} else {
						if (is_array($parameter)) {
							$type = 'a';
						} else {
							$type = 's';
						}
					}

					if ($type == 'a') {
						$return = false;

						foreach ($parameter as $key => $value) {
							$parameters[] = $value;

							if ($return) {
								$return .= ',?';
							} else {
								$return = '?';
							}
							$types .= 's';
						}

						return $return;
					} else {
						if (! $type) {
							$type = 's';
						}
						$types .= $type;

						$parameters[] = $parameter;
					}
				} else {
					return 'null';
				}
			}

			return '?';
		},
		$sql);

		return [$parameters, $types];
	}

	// Prepare
	public function execute($sql, $params = [], $paramTypes = []) {
		list($sql, $params) = Event::trigger(__CLASS__,__FUNCTION__, $sql, $params);
		//save orig sql for debugging info
		$origSql = $sql;

		list($parameters, $types) = $this->paramsToQmark($sql, $params, $paramTypes);

		try {
			$stmt = $this->prepare($sql);
		} catch (\mysqli_sql_exception $e) {
			$message = $e->getMessage() . "\n" . $this->debugSql($origSql, $params, $paramTypes) . "\n - " . $origSql;

			throw new \Exception($message, $e->getCode());
		}

		if ($stmt && ! empty($types)) {
			array_unshift($parameters, $types);

			//hack for php 7.x bind_param "expected to be a reference, value given" stupid warning
			$referenceArray = [];

			foreach ($parameters as $key => $value) {
				$referenceArray[$key] = &$parameters[$key];
			}

			@call_user_func_array([$stmt, 'bind_param'], $referenceArray);
		} else {
			error_log($this->error . ' ' . $this->debugSql($origSql, $params, $paramTypes));
		}

		if (DEBUG) {
			error_log($this->debugSql($origSql, $params, $paramTypes));
		}

		if ($stmt) {
			try {
				if ($stmt->execute()) {
					return $stmt;
				} else {
					error_log(print_r($stmt, 1));
					error_log($this->debugSql($sql, $params, $paramTypes));
				}
			} catch (\mysqli_sql_exception $e) {
				$message = $e->getMessage() . "\n" . $origSql . "\n" . $this->debugSql($origSql, $params, $paramTypes) . "\n" . print_r($parameters, 1) . $types;

				throw new \Exception($message, $e->getCode());
			}
		} else {
			error_log(print_r($stmt, 1));
			error_log($this->debugSql($origSql, $params, $paramTypes));
		}

		return $stmt;
	}

	public function now() {
		return 'NOW()';
	}

	// Bind
	public function bind($param, $value, $type = null) {
		$this->stmt->bindValue($param, $value, $type);
	}

	public function debugSql($sql, $params = [], $paramTypes = []) {
		$sql = preg_replace_callback(
		SQL_VAR_REGEX,
		function ($matches) use (&$params, &$types, $paramTypes) {
			//if parameters is array element
			$varName = $matches[1];
			$value = $params[$varName] ?? '';

			if (strpos($varName, '.') !== false) {
				$varName = $this->sqlPhpArrayKey($varName);
			}

			if ($varName[0] == '[') {
				if (preg_match_all('/[\w_-]+/', $varName, $arrayKeys)) {
					$type = $paramTypes[$varName] ?? 's';

					$types .= $type;

					$key1 = $arrayKeys[0][0];
					$key2 = $arrayKeys[0][1];

					$parameter = &$params[$key1][$key2];
					//if (strpos($parameter, ')') && )
					if ($type == 's') {
						$parameter = '"' . (string)$parameter . '"';
					}

					return $parameter;
				}
			} else {
				if (isset($params[$varName])) {
					$parameter = &$params[$varName];

					if (isset($paramTypes[$varName])) {
						$type = $paramTypes[$varName];
					} else {
						if (is_array($parameter)) {
							$type = 'a';
						} else {
							$type = 's';
						}
					}

					if ($type == 'a') {
						$return = false;

						if ($parameter) {
							foreach ($parameter as $key => $value) {
								if ($return) {
									$return .= ',';
								}

								if ($value) {
									$return .= '"' . $value . '"';
								}
							}
						}

						return $return;
					} else {
						if (! $type) {
							$type = 's';
						}
						$types .= $type;
					}

					if (! $type) {
						$type = 's';
					}
					$types .= $type;

					if ($type == 's') {
						$parameter = '"' . $parameter . '"';
					}

					return $parameter;
				} else {
					return 'null';
				}
			}

			return '?';
		},
		$sql);

		return $sql;
	}

	public function upsert($sql, $params, $paramTypes = []) {
	}
}
