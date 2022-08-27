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

//require_once 'psttt.php';
//define('THEME_PATH', '/home/store/themes/vvveb/');

namespace Vvveb\System\Core;

use function Vvveb\config;
use Vvveb\System\Component\Component;
use Vvveb\System\Event;
use Vvveb\System\Sites;

include DIR_SYSTEM . VIEW_TEMPLATE_ENGINE . '.php';

class View {
	static private $instance;

	private $templatePath;

	private $theme;

	private $htmlPath;

	private $template;

	private $htmlTemplate;

	private $templateEngine;

	private $compiledTemplate;

	private $serviceTemplate;

	private $component;

	private $componentCount;

	private $componentContent;

	private $staging;

	private $useComponent;

	function getTemplateEngineInstance() {
		if (! $this->templateEngine) {
			self :: getInstance();
		}

		return $this->templateEngine;
	}

	static function getInstance() {
		if (self :: $instance === NULL) {
			self :: $instance = new self(); //create class instance

			//self :: $instance->theme        = \Vvveb\config(APP . '.theme', 'default');
			if (APP == 'app') {
				self :: $instance->theme        = Sites::getTheme() ?? 'default';
			} else {
				self :: $instance->theme        = config(APP . '.theme', 'default');
			}

			self :: $instance->htmlPath     = DIR_THEME . self :: $instance->theme . DIRECTORY_SEPARATOR;
			self :: $instance->templatePath = DIR_THEME . self :: $instance->theme . DIRECTORY_SEPARATOR; //\Vvveb\config(APP . '.theme', 'default') . DIRECTORY_SEPARATOR;

			if (isset($_REQUEST['_component_ajax'])) {
				self :: $instance->component        = \Vvveb\filter('/[a-z\-]*/', $_REQUEST['_component_ajax'], 15);
				self :: $instance->componentCount   = \Vvveb\filter('/\d+/', $_REQUEST['_component_id'],  2);
				//if (isset($_REQUEST['_server_template'])) {
				self :: $instance->componentContent = $_POST['_component_content'] ?? '';
				//}
			}

			$selector = $count = null;

			if (isset(self :: $instance->component)) {
				$selector = '[data-v-component-' . self :: $instance->component . ']';
			}

			$templateEngine = VIEW_TEMPLATE_ENGINE;

			$template = new $templateEngine($selector, self :: $instance->componentCount, self :: $instance->componentContent);

			$template->addTemplatePath(DIR_TEMPLATE);
			$template->addTemplatePath(self :: $instance->htmlPath . 'template' . DIRECTORY_SEPARATOR);
			$template->htmlPath     =  self :: $instance->htmlPath;

			self :: $instance->templateEngine = $template;

			/*
			putenv('LC_ALL=ro_RO');
			setlocale(LC_ALL, 'ro_RO.utf8');
			clearstatcache();
			//set gettext domain
			//echo $this->theme . "<br>\n";
			//echo $this->htmlPath . "locale<br>\n";
			bindtextdomain($this->theme, $this->htmlPath . 'locale');
			textdomain($this->theme);
			 */

			//echo gettext('Powered by');
		}

		return self :: $instance;
	}

	function setTheme($theme) {
		$theme            = \Vvveb\filter('/[a-z0-9-]*/', $theme, 15);
		$this->theme      = $theme;
		$this->htmlPath   = DIR_THEME . $theme . DIRECTORY_SEPARATOR;
	}

	function getTheme() {
		return $this->theme;
	}

	function getTemplatePath() {
		return $this->templatePath;
	}

	function compiledTemplate() {
		return $this->compiledTemplate;
	}

	function set(&$data) {
		if ($data && is_array($data)) {
			foreach ($data as $key => &$value) {
				$this->$key = $value;
			}
		}
	}

	function serviceTemplate() {
		return $this->serviceTemplate;
	}

	function checkNeedRecompile() {
		if (empty($this->template)) {
			//	return false;
		}

		$templatePath      = $this->templatePath;
		$template          = $this->template;
		$templateMtime     = null;
		$templateFile      = $templatePath . $template;
		$html              = DIR_TEMPLATE . $this->htmlTemplate;

		if (strpos($this->template, 'plugins' . DIRECTORY_SEPARATOR) === 0) {
			$templatePath = DIR_PUBLIC . 'plugins' . DIRECTORY_SEPARATOR;

			$template   = str_replace('plugins' . DIRECTORY_SEPARATOR, '', $template);
			$p          = strpos($template, DIRECTORY_SEPARATOR);
			$pluginName = substr($template, 0, $p);
			$nameSpace  = substr($template, $p + 1);

			if (APP == 'admin') {
				$tpl      = $pluginName . DIRECTORY_SEPARATOR . join(DIRECTORY_SEPARATOR, ['admin', 'template']) . DIRECTORY_SEPARATOR . $nameSpace;
				$template = $pluginName . DIRECTORY_SEPARATOR . 'admin' . DIRECTORY_SEPARATOR . $nameSpace;
			}

			$this->htmlTemplate = str_replace('.html', '.tpl', $tpl);
			$html               = DIR_PLUGINS . $this->htmlTemplate;
			$templateFile       = $templatePath . $template;
		}

		if (! file_exists($templatePath . $template)) {
			if ($template == 'error404.html' || $template == 'error500.html') {
				//if theme is missing error page then use the default
				$templateFile = DIR_PUBLIC . $template;
			} else {
				FrontController::notFound(false, 404,  [
					'message' => 'Template not found!',
					'file'    => $templatePath . $template,
				]);
			}
		}

		if (file_exists($templateFile)) {
			$templateMtime = filemtime($templateFile);
		}

		$htmlMtime = 0;

		if (file_exists($html)) {
			$htmlMtime = filemtime($html);
		}

		$compiledMtime = 0;

		if (file_exists($this->compiledTemplate)) {
			$compiledMtime = @filemtime($this->compiledTemplate);
		}

		if ((max($templateMtime, $htmlMtime) > $compiledMtime
			 || ! file_exists($this->compiledTemplate))
			|| (defined('DEBUG') && DEBUG) || isset($_POST['_component_content'])) {
			$this->recompile($templateFile, $this->compiledTemplate);
		}
	}

	private function recompile($filename, $file) {
		@touch($file); //if recompiling takes longer avoid avoid other recompile requests
		//regenerate component file
		//if (APP== 'store')
		if ($this->useComponent && ! defined('CLI')) {
			//regenerate components cache
			$service = Component::getInstance(true, $this->componentContent);
		}

		//$psttt = new psttt($selector, $this->componentCount);
		$errors = $this->templateEngine->loadHtmlTemplate($filename);

		//if no template defined use the default
		if (strpos($this->htmlTemplate, 'plugins' . DIRECTORY_SEPARATOR) === 0) {
			$template   = str_replace('plugins' . DIRECTORY_SEPARATOR, '', $this->htmlTemplate);
			$p          = strpos($template, DIRECTORY_SEPARATOR);
			$pluginName = substr($template, 0, $p);
			$nameSpace  = substr($template, $p + 1);

			$this->htmlTemplate = $pluginName . DIRECTORY_SEPARATOR . APP . DIRECTORY_SEPARATOR . 'template' . DIRECTORY_SEPARATOR . $nameSpace;
			$this->templateEngine->loadTemplateFile(DIR_PLUGINS . $this->htmlTemplate);
		/*
		if (APP == 'admin') {
			$this->htmlTemplate = $pluginName . DIRECTORY_SEPARATOR . APP ."/template/$nameSpace";
		} else {
			$this->htmlTemplate = "$pluginName/admin/template/$nameSpace";
		}*/
		} else {
			if (! file_exists(DIR_TEMPLATE . $this->htmlTemplate)) {
				$this->htmlTemplate = 'common.tpl';
			}

			$this->templateEngine->loadTemplateFileFromPath($this->htmlTemplate);
		}

		$this->templateEngine->saveCompiledTemplate($file);
	}

	function htmlTemplate($filename) {
		$this->htmlTemplate = $filename;
	}

	function template($filename = null) {
		if ($filename === false) {
			$this->template = false;

			return;
		}

		if ($filename) {
			$filename = str_replace('..', '', $filename);

			$compiledFilename = DIR_COMPILED_TEMPLATES
			. APP . '_' . (defined('SITE_ID') ? SITE_ID : '-') . '_'
			. ((is_null($this->component)) ? '' : $this->component . $this->componentCount . '_')
			. $this->theme . '_'
			. str_replace([DIRECTORY_SEPARATOR, '/', '\\'] , '_', $filename);

			$this->compiledTemplate        = $compiledFilename;
			$this->serviceTemplate         = $compiledFilename;
			$this->htmlTemplate            = str_replace('.html', '.tpl', $filename);
			$this->template                = $filename;
		}

		return $this->template;
	}

	function fragment($selector, $index = 0) {
		$this->component      = $selector;
		$this->componentCount = $index;
	}

	function noJson($value = true) {
		$this->noJson = $value;
	}

	function render($useComponent = true) {
		Event :: trigger(__CLASS__,__FUNCTION__);

		$this->useComponent = $useComponent;

		if ($useComponent && ! defined('CLI')) {
			$service = Component::getInstance(false, $this->componentContent);
		}

		//json
		if (defined('FORCE_JSON') && FORCE_JSON) {
			echo json_encode($this, JSON_PRETTY_PRINT);

			return;
		}

		if (false && isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest' &&
		! isset($this->noJson) && ! isset($_REQUEST['_component_ajax'])) {
			unset($this->config);
			ob_start();
			header('Content-type: application/json');
			echo json_encode($this, JSON_PRETTY_PRINT);
			ob_end_flush();

			return;
		} else { //html
			if (! $this->template) {
				self::template();
			}
			$this->checkNeedRecompile();

			if (! file_exists($this->compiledTemplate)) {
				return FrontController::notFound();
			}

			include_once $this->compiledTemplate;
		}
	}
}
