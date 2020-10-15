<?php

import('lib.pkp.classes.plugins.GenericPlugin');

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
	 * @return false
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

