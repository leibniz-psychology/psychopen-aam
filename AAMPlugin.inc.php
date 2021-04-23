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
				$request = Application::getRequest();
				$templateMgr = TemplateManager::getManager($request);
				$templateMgr->addJavaScript('AAMPluginScript', $request->getBaseUrl().'/'.$this->getPluginPath().'/js/aam.js');
				define('HANDLER_CLASS', 'AAMPluginHandler');
				$args[2] = $this->getPluginPath().'/handler/AAMPluginHandler.inc.php';
				break;
		}
	}



	/**
	 * Add settings button to plugin
	 * @param $request
	 * @param array $verb
	 * @return array
	 */
	public function getActions($request, $verb)
	{
		$router = $request->getRouter();
		import('lib.pkp.classes.linkAction.request.AjaxModal');
		return array_merge(
			$this->getEnabled() ? array(
				new LinkAction(
					'settings',
					new AjaxModal(
						$router->url($request, null, null, 'manage', null, array('verb' => 'settings', 'plugin' => $this->getName(), 'category' => 'generic')),
						$this->getDisplayName()
					),
					__('manager.plugins.settings'),
					null
				),
			) : array(),
			parent::getActions($request, $verb)
		);
	}

	public function manage($args, $request)
	{
		switch ($request->getUserVar('verb')) {
			case 'settings':
				$this->import('AAMPluginSettingsForm');
				$form = new AAMPluginSettingsForm($this);
				if (!$request->getUserVar('save')) {
					$form->initData();

					return new JSONMessage(true, $form->fetch($request));
				}
				$form->readInputData();
				if ($form->validate()) {
					$form->execute();

					return new JSONMessage(true);
				}
		}

		return parent::manage($args, $request);
	}

}

