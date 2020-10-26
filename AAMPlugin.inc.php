<?php

import('lib.pkp.classes.plugins.GenericPlugin');


/**
 * This file is part of Forthcoming Articles Plugin (https://github.com/leibniz-psychology/pkp-openid).
 *
 * Forthcoming Articles Plugin is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Forthcoming Articles Plugin is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Forthcoming Articles Plugin.  If not, see <https://www.gnu.org/licenses/>.
 *
 * Copyright (c) 2020 Leibniz Institute for Psychology Information (https://leibniz-psychology.org/)
 *
 * @file plugins/generic/psychopen-aam/AAMPlugin.inc.php
 * @ingroup plugins_generic_aam
 * @brief AAMPlugin class for plugin and handler registration
 *
 */
class AAMPlugin extends GenericPlugin
{


	/**
	 * Get the display name of this plugin
	 * @return string
	 */
	function getDisplayName()
	{
		return __('plugins.generic.aam.name');
	}

	/**
	 * Get the description of this plugin
	 * @return string
	 */
	function getDescription()
	{
		return __('plugins.generic.aam.description');
	}


	/**
	 * Register the plugin, if enabled
	 *
	 * @param $category
	 * @param $path
	 * @param $mainContextId
	 * @return true on success
	 */
	public function register($category, $path, $mainContextId = null)
	{
		$success = parent::register($category, $path);
		if ($success && $this->getEnabled()) {
			HookRegistry::register('LoadHandler', array($this, 'callbackLoadHandler'));
		}

		return $success;
	}

	/**
	 * Loads Handler for login, registration, sign-out and the plugin specific urls.
	 * Adds JavaScript and Style files to the template.
	 *
	 * @param $hookName
	 * @param $args
	 */
	public function callbackLoadHandler($hookName, $args)
	{
		$page = $args[0];
		define('AAM_PLUGIN_NAME', $this->getName());
		switch ("$page") {
			case 'aam':
				$request = Application::get()->getRequest();
				$templateMgr = TemplateManager::getManager($request);
				$templateMgr->addJavaScript('AAMPluginScript', $request->getBaseUrl().'/'.$this->getPluginPath().'/js/aam.js');
				define('HANDLER_CLASS', 'AAMPluginHandler');
				$args[2] = $this->getPluginPath().'/handler/AAMPluginHandler.inc.php';
				break;
		}
	}

}

