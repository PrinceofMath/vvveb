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

namespace Vvveb\Component;

use Vvveb\System\Component\ComponentBase;
use Vvveb\System\Event;

class Stats extends ComponentBase {
	public static $designOnly = false;

	public static $defaultOptions = [
		'start'       => 0,
		'count'       => 10,
		'language_id' => 1,
		'site_id'     => 1,
		'stat_id'     => 'url',
		'stat'        => ['url', 'price asc'],
		'id_category' => 'url',
		'id'          => NULL,
		'start_date'  => '',
		'end_date'    => 'NOW()',
		'range'       => 'day', //day, week, month, year
	];

	public $options = [];

	function results() {
		$stats = new \Vvveb\Sql\StatSQL();

		$results = $stats->getStats($this->options);
		$data    = [];

		foreach ($results['orders'] as $order) {
			$data[$order['date']]['orders'] = $order['orders'];
		}

		foreach ($results['customers'] as $order) {
			$data[$order['date']]['customers'] = $order['customers'];
		}

		ksort($data);
		$labels = array_keys($data);

		$customers =[];
		$orders    =[];

		foreach ($data as $date => $stat) {
			$customers[] = $stat['customers'] ?? 0;
			$orders[]    = $stat['orders'] ?? 0;
		}

		return ['labels' => $labels, 'customers' => $customers, 'orders' => $orders];

		switch ($range) {
			default:
			case 'day':
				$results = $this->getTotalOrdersByDay();

				foreach ($results as $key => $value) {
					$json['order']['data'][] = [$key, $value['total']];
				}

				$results = $this->getTotalCustomersByDay();

				foreach ($results as $key => $value) {
					$json['customer']['data'][] = [$key, $value['total']];
				}

				for ($i = 0; $i < 24; $i++) {
					$json['xaxis'][] = [$i, $i];
				}

				break;

			case 'week':
				$results = $this->getTotalOrdersByWeek();

				foreach ($results as $key => $value) {
					$json['order']['data'][] = [$key, $value['total']];
				}

				$results = $this->getTotalCustomersByWeek();

				foreach ($results as $key => $value) {
					$json['customer']['data'][] = [$key, $value['total']];
				}

				$dateStart = strtotime('-' . date('w') . ' days');

				for ($i = 0; $i < 7; $i++) {
					$date = date('Y-m-d', $dateStart + ($i * 86400));

					$json['xaxis'][] = [date('w', strtotime($date)), date('D', strtotime($date))];
				}

				break;

			case 'month':
				$results = $this->getTotalOrdersByMonth();

				foreach ($results as $key => $value) {
					$json['order']['data'][] = [$key, $value['total']];
				}

				$results = $this->getTotalCustomersByMonth();

				foreach ($results as $key => $value) {
					$json['customer']['data'][] = [$key, $value['total']];
				}

				for ($i = 1; $i <= date('t'); $i++) {
					$date = date('Y') . '-' . date('m') . '-' . $i;

					$json['xaxis'][] = [date('j', strtotime($date)), date('d', strtotime($date))];
				}

				break;

			case 'year':
				$results = $this->getTotalOrdersByYear();

				foreach ($results as $key => $value) {
					$json['order']['data'][] = [$key, $value['total']];
				}

				$results = $this->getTotalCustomersByYear();

				foreach ($results as $key => $value) {
					$json['customer']['data'][] = [$key, $value['total']];
				}

				for ($i = 1; $i <= 12; $i++) {
					$json['xaxis'][] = [$i, date('M', mktime(0, 0, 0, $i))];
				}

				break;
		}

		list($results) = Event :: trigger(__CLASS__,__FUNCTION__, $results);

		return $results;
	}
}
