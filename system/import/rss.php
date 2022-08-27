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

namespace Vvveb\System\Import;

class Rss {
	private  $dom;

	private  $xpath;

	private $importXMLOptions = LIBXML_NOBLANKS |
								LIBXML_COMPACT |
								LIBXML_NOCDATA |
								LIBXML_NOENT |
								LIBXML_NONET |
								LIBXML_PARSEHUGE |
								LIBXML_NOWARNING;

	// |
	//LIBXML_BIGLINES;

	function __construct($rss) {
		$this->rss                     = $rss;
		$this->dom                     = new \DOMDocument('1.0', 'utf-8');
		$this->dom->formatOutput       = false;
		$this->dom->preserveWhitespace = false;
		$this->dom->loadXML($this->rss, $this->importXMLOptions);
		$this->dom->normalize();

		$this->xpath  = new \DOMXpath($this->dom);
	}

	function buildXPath($start = 1, $limit = 10, $attributes = []) {
		$list = '';

		foreach ($attributes as $name => $value) {
			$operator = '=';

			if (is_array($value)) {
				$value    = $value[0];
				$operator = $value[1];
			}
			$list .= "[$name $operator '$value']";
		}

		$xpath = "//channel/item$list";

		return $xpath;
	}

	function count($attributes = []) {
		$items = $this->xpath->query($this->buildXPath($attributes));

		return $items->length ?? 0;
	}

	function get($start = 1, $limit = 10, $attributes = []) {
		$attributes[] = ['position()'=> [$start, '>=']];
		$attributes[] = ['position()'=> [$limit, '<=']];

		$items = $this->xpath->query($this->buildXPath($attributes));

		foreach ($items as $item) {
			$columns = $item->childNodes;

			foreach ($columns as $column) {
				if ($column->nodeName == '#text') {
					continue;
				}
				$columnName  = $column->nodeName;
				$columnValue = $column->nodeValue;

				$row[$columnName] = $columnValue;
			}

			$rows[] = $row;
		}

		return $rows;
	}
}
