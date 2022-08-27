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

namespace Vvveb\System\SqlP;

class Lexer {
	protected $regex;

	protected $offsetToToken;

	public function __construct(array $tokenMap) {
		$this->regex         = '/(' . implode(')|(', array_keys($tokenMap)) . ')/As';
		$this->offsetToToken = array_values($tokenMap);
	}

	public function lex($string) {
		$tokens = [];

		$offset = 0;
		$sql    = '';

		while (isset($string[$offset])) {
			if (! preg_match($this->regex, $string, $matches, null, $offset)) {
				throw new Exception(sprintf('Unexpected character "%s" at offset %d', $string[$offset], $offset));
			}

			// find the first non-empty element (but skipping $matches[0]) using a quick for loop
			for ($i = 1; '' === $matches[$i]; ++$i);
			$token = $this->offsetToToken[$i - 1];
			//gather all sql tokens into one continuous string
			if ($token == 'T_SQL') {
				$sql .= $matches[0];
			} else {
				if ($sql) {
					$tokens[] = [$sql, 'T_SQL', $offset];
					$sql      = '';
				}
				$tokens[] = [$matches[0], $token, $offset];
			}

			if ($sql) {
				$tokens[] = [$sql, 'T_SQL', $offset];
				$sql      = '';
			}

			$offset += strlen($matches[0]);
		}

		return $tokens;
	}

	// a recursive function to build the ast structure
	function tree(&$structure, $i=0) {
		$output = [];
		$count  = count($structure);
		//var_dump($structure);
		for ($i; $i < $count; $i++) {
			list($element, $type, $offset) = $structure[$i];

			$node = $structure[$i];

			if ($type == 'T_IF_START') {
				$ret              = $this->tree($structure, $i + 1);
				$node['children'] = $ret[0];
				$i                = $ret[1];
			} else {
				if ($type == 'T_IF_END' || $type == 'T_EACH_END') {
					$output[] = $node;

					return [$output, $i];
				} else {
					if ($type == 'T_SQL') {
					}
				}
			}

			$output[] = $node;
		}

		return $output;
	}

	// a recursive function to build the ast structure
	function treeMacro(&$structure, $i=0, $level = 0) {
		$output = [];
		$count  = count($structure);
		//var_dump($structure);
		for ($i; $i < $count; $i++) {
			list($element, $type, $offset) = $structure[$i];

			$node = $structure[$i];

			if ($type == 'T_IF_START' || $type == 'T_ELSE') {
				$ret              = $this->tree($structure, $i + 1, $level + 1);
				$node['children'] = $ret[0];
				$i                = $ret[1];
			} else {
				if (($type == 'T_IF_END' || $type == 'T_EACH_END') && $level > 0) {
					$output[] = $node;

					return [$output, $i];
				} else {
					if ($type == 'T_SQL') {
					}
				}
			}

			$output[] = $node;
		}

		return $output;
	}

	function treeToSql(&$tree) {
		$sql = '';

		foreach ($tree as $token) {
			$sql .= $token[0];

			if (isset($token['children'])) {
				$sql .= $this->treeToSql($token['children']);
			}
		}

		return $sql;
	}

	function parseMacro($statement, $regex, $template) {
		$macro = $template;

		if (preg_match_all($regex, $statement, $matches, PREG_SET_ORDER)) {
			foreach ($matches as $match) {
				$macro = $template;

				//replace macro template variables %$variable
				$macro = preg_replace_callback(
				'@\$%(\w+)@',
				function ($varMatch) use ($match) {
					return
					preg_replace_callback(
						'/:(\w+)/ms',
						function ($matches) {
							return '$params[\'' . $matches[1] . '\']';
						},
					$match[$varMatch[1]]);
				},
				$macro);

				//replace macro template placeholders %placeholder
				$macro = preg_replace_callback(
				'@\%(\w+)@',
					function ($varMatch) use ($match) {
						return $match[$varMatch[1]];
					},
				$macro);

				$statement = str_replace($match[0], $macro, $statement);
			}
		}

		return $statement;
	}

	function treeToPhp(&$tree, &$macroMap) {
		$sql = '';

		foreach ($tree as $token) {
			$name = $token[1];
			$code = $token[0];

			if ($name == 'T_SQL') {
				$sql .= $code;
			} elseif (isset($macroMap[$name])) {
				$macro = $macroMap[$name];

				if (is_array($macro)) {
					$regex  =  $macro[0];
					$string =  $macro[1];

					$sql .= $this->parseMacro($code, $regex, $string);
				} else {
					$sql .= $macro;
				}
			}

			if (isset($token['children'])) {
				$sql .= $this->treeToPhp($token['children'], $macroMap);
			}
		}

		return $sql;
	}
}

$tokenMap = [
	'@IF+\s*.+?\s*THEN' => 'T_IF_START',
	'@ELSE'             => 'T_ELSE',
	'END @IF'           => 'T_IF_END',
	'@KEYS\(.+?\)'      => 'T_KEYS',
	'@LIST\(.+?\)'      => 'T_LIST',
	//'@EACH\(.+?\)'      => 'T_EACH_START',
	//'END @EACH'         => 'T_EACH_END',
	'@SQL_COUNT\(.+?\)' => 'T_SQL_COUNT',
	//'@[[:upper:]]+'     => 'T_MACRO_START',
	//'END @\w+'          => 'T_MACRO_END',
	'.+?'               => 'T_SQL',
];

$macroMap = [
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
	//'T_MACRO_START'     => '{',
	//'T_MACRO_END'       => '}',
	'T_SQL'             => '',
];
