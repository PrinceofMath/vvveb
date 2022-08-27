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

namespace Vvveb\System\Component;

use Vvveb\System\Cache;
use Vvveb\System\Core\View;

if (! defined('COMPONENT_CACHE_FLAG_LOCK')) {
	define('COMPONENT_CACHE_FLAG_LOCK', PHP_INT_MIN + 1);
	define('COMPONENT_CACHE_FLAG_REGENERATE', PHP_INT_MIN + 2);

	//time
	define('COMPONENT_CACHE_EXPIRE_DELAY', 5); //real expiration time +5 seconds
	define('COMPONENT_CACHE_WAIT', 1); //wait for cache generation
	define('COMPONENT_CACHE_MAX_WAIT_RETRY', 3); //wait for cache generation
	define('COMPONENT_CACHE_LOCK_EXPIRE', 20); //lock can not be set more than COMPONENT_CACHE_LOCK_EXPIRE seconds
	define('COMPONENT_CACHE_EXPIRE', 20);
}

class Component {
	private static $queue;

	private static $components;

	private static $componentsFile = null;

	private static $loaded = false;

	private static $content = false;

	static function getInstance($regenerate = false, $content = false) {
		self::$componentsFile = View::getInstance()->serviceTemplate() . '.components';
		self::$content        = $content;

		if ((! file_exists(self::$componentsFile)) || $regenerate) {
			self::generateRequiredComponents();
			self::saveTemplateComponents();
			self :: loadComponents();
			self::$loaded = true;
		} else {
		}

		if (self::$loaded) {
			return true;
		}

		self::loadTemplateComponents();
		self::loadComponents();
		self::$loaded = true;
	}

	static function getComponent($name) {
		return self::$components[$name] ?? [];
	}

	static function loadComponents() {
		$view = view::getInstance();

		if (isset(self::$components)) {
			self::$components = array_merge([
				'config' => [
					0 => NULL,
				],
			] , self::$components);
		} else {
			self::$components = [
				'config' => [
					0 => NULL,
				],
			];
		}

		$cache = [];
		$objs  = [];

		$notFound404 = false;

		if (is_array(self::$components)) {
			foreach (self::$components as $component => $instances) {
				$class          = '\Vvveb\Component\\' . str_replace('-', '\\', $component);
				$file           = DIR_APP . '/component/' . str_replace('-', '/', $component) . '.php';

				$component = str_replace('-', '_', $component);

				if (file_exists($file)) {
					include_once $file;

					foreach ($instances as $instance => $options) {
						$obj      = new $class($options);
						$cacheKey = $obj->cacheKey();

						if ($cacheKey) {
							$cacheExpireKey                  = 'expire_' . $cacheKey;
							$cache[]                         = $cacheKey;
							$cache[]                         = $cacheExpireKey;
							$obj->component                  = $component;
							$objs[$obj->cacheKey][$instance] = $obj;
						} else {
							$results = $obj->results();

							if ($results !== false && $results !== null) {
								$results['_instance'] = $obj;
							}

							$comp            = &$view->$component;
							$comp[$instance] = $results;

							if (isset($results['404']) && $results['404'] == true) {
								$notFound404 = true;
							}
						}
					}
				}
			}

			//get cached data
			$cacheDriver = Cache::getInstance();
			//$cacheDriver = new Memcached();
			$null = [];
			$data = $cacheDriver->getMulti($cache, SITE_ID);

			foreach ($objs as $cacheKey => $instances) {
				foreach ($instances as $index => $instance) {
					$component                    = $instance->component;
					$component                    = str_replace('-', '_', $component);

					$data[$cacheKey]['_instance'] = $instance;
					$comp                         = &$view->$component;
					$comp[$index]                 = $data[$cacheKey];

					if (isset($comp[$index]['404']) && $comp[$index]['404'] == true) {
						$notFound404 = true;
					}
				}

				//cache hit, remove from sql regeneration queue
				$cacheExpireKey = 'expire_' . $cacheKey;

				//if no lock set (! -1) and cache is expiring then set lock and set for regeneration
				if (! isset($data[$cacheExpireKey]) || ! isset($data[$cacheKey]) ||
					($data[$cacheExpireKey] && ($data[$cacheExpireKey] > 0) &&
					($data[$cacheExpireKey] + COMPONENT_CACHE_EXPIRE_DELAY) < $_SERVER['REQUEST_TIME'])) {
					$cacheDriver->set($cacheExpireKey, COMPONENT_CACHE_FLAG_LOCK, COMPONENT_CACHE_LOCK_EXPIRE); //set lock
					$data[$cacheExpireKey] = COMPONENT_CACHE_FLAG_REGENERATE; //set regeneration flag
					//error_log($data[$cacheExpireKey] . ' regeneration' . $cacheExpireKey);
				}

				if ($data[$cacheExpireKey] > 0) {
					unset($objs[$cacheKey], $cache[$cacheKey], $cache[$cacheExpireKey], $data[$cacheKey], $data[$cacheExpireKey]);
				}
			}

			$wait  = true;
			$retry = 0;
			//run sql queries for uncached components
			$saveCache = [];

			while ($wait && $retry < COMPONENT_CACHE_MAX_WAIT_RETRY) {
				$wait = false;
				$retry++;

				foreach ($objs as $key => $objects) {
					$cacheExpireKey = 'expire_' . $key;
					//error_log($cacheExpireKey . ' --- waiTTTTT ' . $data[$cacheExpireKey]);
					//check lock
					if (! isset($data[$cacheExpireKey]) || ! isset($data[$key]) || $data[$cacheExpireKey] == COMPONENT_CACHE_FLAG_REGENERATE) {
						//lock set by this script, regenerate content
						$id      = key($objects);

						$results = $objects[$id]->results();

						if ($results !== null) {
							$saveCache[$key]            = $results;
							$saveCache[$cacheExpireKey] = $_SERVER['REQUEST_TIME'] + $objects[$id]->cacheExpire;
						}

						if (isset($results['404']) && $results['404'] == true) {
							$notFound404 = true;
						}

						$data[$cacheExpireKey] = 0;

						foreach ($objects as $index => $instance) {
							$results['_instance'] = $instance;
							$component            = $instance->component;
							$component            = str_replace('-', '_', $component);
							$comp                 = &$view->$component;
							$comp[$index]         = $results;
						}
					} else {
						if ($data[$cacheExpireKey] == COMPONENT_CACHE_FLAG_LOCK) {
							//error_log("wait for $cacheExpireKey");
							//item is locked, some other script is generating content
							$wait = true;
						}
					}
				}

				if ($wait) {
					error_log('wait cache ' . $_SERVER['REQUEST_URI'] . print_r($cache,1));
					//get
					@sleep(COMPONENT_CACHE_WAIT);
					$data = $cacheDriver->getMulti($cache);
				}
			}

			if ($retry >= COMPONENT_CACHE_MAX_WAIT_RETRY) {
				error_log('error:CACHE max retry reached for ' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
			}
		}

		if (! empty($saveCache)) {
			$cacheDriver->setMulti($saveCache, $_SERVER['REQUEST_TIME'] + COMPONENT_CACHE_EXPIRE, SITE_ID);
		}

		//call request for each component
		foreach (self::$components as $component => $instances) {
			foreach ($instances as $index => $options) {
				if (isset($view->$component)) {
					$component = str_replace('-', '_', $component);
					$comp      = &$view->$component;
					$results   = $comp[$index];
					$object    = $results['_instance'];

					if (method_exists($object, 'request')) {
						$object->request($results);
					}
				}
			}
		}

		unset($data, $cache);

		self::$components = NULL;

		if ($notFound404) {
			FrontController::notFound(false);
		}
	}

	/*	static  function isLoaded($component)
	{
	return isset(self :: $queue[$component]);
	}
	*/
	static function results() {
	}

	static function generateRequiredComponents() {
		//get components from html page
		$document                      = new \DomDocument();
		$document->preserveWhiteSpace  = false;
		$document->recover             = true;
		$document->strictErrorChecking = false;
		$document->formatOutput        = false;
		$document->resolveExternals    = false;
		$document->validateOnParse     = false;
		$document->xmlStandalone       = true;

		$view = view::getInstance();
		libxml_use_internal_errors(true);

		if (self::$content) {
			@$document->loadHTML(self::$content);
		} else {
			$view     = View::getInstance();
			$template = $view->template();

			if (strpos($template, 'plugins/') === 0) {
				$template   = str_replace('plugins/', '', $template);
				$p          = strpos($template, '/');
				$pluginName = substr($template, 0, $p);
				$nameSpace  = substr($template, $p + 1);

				$template = DIR_PLUGINS . $pluginName . '/public/' . APP . "/$nameSpace";
			} else {
				$template = $view->getTemplatePath() . $template;
			}

			@$document->loadHTMLFile($template,
						LIBXML_NOWARNING | LIBXML_NOERROR);
		}

		$xpath = new \DOMXpath($document);

		//include froms in case any component_ is included
		$elements = $xpath->query('//*[ @data-v-copy-from ]');

		if ($elements && $elements->length) {
			$fromDocument                      = new \DomDocument();
			$fromDocument->preserveWhiteSpace  = false;
			$fromDocument->recover             = true;
			$fromDocument->strictErrorChecking = false;
			$fromDocument->formatOutput        = false;
			$fromDocument->resolveExternals    = false;
			$fromDocument->validateOnParse     = false;
			$fromDocument->xmlStandalone       = true;

			foreach ($elements as $element) {
				if (preg_match('/([^\,]+)\,([^\s$,]+)/', $element->getAttribute('data-v-copy-from') , $from)) {
					$file     = $from[1];
					$selector = $from[2];

					$fromDocument->loadHTMLFile($view->getTemplatePath() . $file);

					$fromXpath = new \DOMXpath($fromDocument);

					$fromElements = $fromXpath->query(\Vvveb\cssToXpath($selector));

					foreach ($fromElements as $externalNode) {
						$importedNode = $document->importNode($externalNode, true);
						$element
							->parentNode
							->replaceChild($importedNode, $element);
					}
				}
			}
		}

		//search for elements that have a class starting with component_
		//$elements = $xpath->query('//*[ contains(@class, "component_") ]');
		$elements = $xpath->query('//*[@*[starts-with(name(), "data-v-component-")]]');

		foreach ($elements as $element) {
			$component = '';
			$opts      = [];

			foreach ($element->attributes as $attr) {
				$nodeName = $attr->nodeName;

				if (strpos($nodeName, 'data-v-component-') === 0) {
					$component = str_replace('data-v-component-', '', $nodeName);
				//$classes = explode(' ', trim($attr->nodeValue));
				} else {
					if (strpos($nodeName, 'data-v-') === 0) {
						$option        = str_replace('data-v-', '', $nodeName);
						$opts[$option] = $attr->nodeValue;
					}
				}
			}

			//get all classes
			//search for options
			$options = null;
			//validate options
			$validOptions   = [];
			$componentClass = '\Vvveb\Component\\' . str_replace('-', '\\', $component);
			$file           = DIR_APP . '/component/' . str_replace('-', '/', $component) . '.php';

			if (file_exists($file)) {
				include_once $file;
				//$componentClass = new $componentClass;
				//do not add design only components
				if (isset($componentClass::$designOnly) && $componentClass::$designOnly == true) {
					continue;
				}
				$validOptions = array_keys($componentClass::$defaultOptions);
			} else {
				if (defined('DEBUG') && \DEBUG) {
					error_log("Component does not exist $componentClass => $file");
				}

				continue;
			}

			//save options
			foreach ($opts as $name => $option) {
				if (in_array($name, $validOptions) && isset($option) !== false) {
					if ((isset($option[0]) && ($option[0] == '{')) || (strpos($option, ',') !== false)) {
						$options[$name] = json_decode($option, 1);
					} else {
						$options[$name] = $option;
					}
				}
			}

			$options['_hash']         = md5($component . serialize($options));
			$components[$component][] = $options;
		}

		if (isset($components)) {
			self::$components = $components;
		}
		//get fields for component
		//load components and feed fields
	}

	static function saveTemplateComponents() {
		$php = var_export(self::$components, true);
		$php = preg_replace('/\s+/', ' ', $php);
		//repeating end lines
		$php = preg_replace('/\n+/', '', $php);

		return file_put_contents(self::$componentsFile, '<?php $components=' . $php . ';');
	}

	static function loadTemplateComponents() {
		include_once self::$componentsFile;

		if (isset($components)) {
			self::$components = $components;
		}
		//keep only requested service
		if (isset($_GET['component_ajax'])) {
		}

		return true;
	}
}
