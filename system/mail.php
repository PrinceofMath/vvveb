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

namespace Vvveb\System;

class Mail {
	private $driver;

	protected $to;

	protected $from;

	protected $sender;

	protected $reply_to;

	protected $subject;

	protected $text;

	protected $html;

	protected $attachments = [];

	public static function getInstance() {
		static $inst = null;

		if ($inst === null) {
			$driver = \Vvveb\config(APP . '.mail.driver', 'file');
			$inst   = new Cache($driver);
		}

		return $inst;
	}

	public function __construct($driver, $expire = 3600) {
		$class = '\\Vvveb\\System\\Mail\\' . $driver;

		$this->expire = $expire;

		if (class_exists($class)) {
			$options      = \Vvveb\config(APP . '.mail', []);
			$this->driver = new $class($options);
		} else {
			throw new \Exception("Error: Could not load mail driver '$driver'!");
		}

		return $this->driver;
	}

	public function setTo($to) {
		$this->to = $to;
	}

	public function setFrom($from) {
		$this->from = $from;
	}

	public function setSender($sender) {
		$this->sender = $sender;
	}

	public function setReplyTo($reply_to) {
		$this->reply_to = $reply_to;
	}

	public function setSubject($subject) {
		$this->subject = $subject;
	}

	public function setText($text) {
		$this->text = $text;
	}

	public function setHtml($html) {
		$this->html = $html;
	}

	public function addAttachment($filename) {
		$this->attachments[] = $filename;
	}

	public function send() : bool {
		if (! $this->to) {
			throw new \Exception('Error: E-Mail to required!');
		}

		if (! $this->from) {
			throw new \Exception('Error: E-Mail from required!');
		}

		if (! $this->sender) {
			throw new \Exception('Error: E-Mail sender required!');
		}

		if (! $this->subject) {
			throw new \Exception('Error: E-Mail subject required!');
		}

		if (! $this->text && ! $this->html) {
			throw new \Exception('Error: E-Mail message required!');
		}

		$mail_data = [];

		foreach (get_object_vars($this) as $key => $value) {
			$mail_data[$key] = $value;
		}

		$mail = new $this->adaptor($mail_data);

		return $mail->send();
	}
}
