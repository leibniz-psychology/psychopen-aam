<?php

import('classes.handler.Handler');


class AAMPluginHandler extends Handler
{

	private const STATUS_QUEUED = 1;
	private const WORKFLOW_STAGE_ID_EDITING = 4;
	private const WORKFLOW_STAGE_ID_PRODUCTION = 5;
	private const DOI_PREFIX = 'https://doi.org/';
	private const PA_BASE_URL = 'https://www.psycharchives.org';

	/**
	 * @param array $args
	 * @param PKPRequest $request
	 */
	function index($args, $request)
	{
		$context = $request->getContext();
		$templateMgr = TemplateManager::getManager($request);
		$plugin = PluginRegistry::getPlugin('generic', AAM_PLUGIN_NAME);
		$contextId = ($context == null) ? 0 : $context->getId();
		$aamItems = [];
		$submissionDao = Application::getSubmissionDAO();
		$submissions = $submissionDao->getByContextId($contextId);
		$pubIdPrefix = $this->_buildDoiPrefix($context);
		while ($submission = $submissions->next()) {
			if ($submission->getStatus() == self::STATUS_QUEUED
				&& ($submission->getStageId() == self::WORKFLOW_STAGE_ID_EDITING
					|| $submission->getStageId() == self::WORKFLOW_STAGE_ID_PRODUCTION)) {
				if (isset($pubIdPrefix)) {
					$paItem = $this->_searchPsychArchivesItemByIsVersionOf($pubIdPrefix.$submission->getId());
					//$paItem = $this->_searchPsychArchivesItemByIsVersionOf('https://doi.org/10.5964/meth.2807');
				}
				if (isset($paItem) && is_array($paItem) && sizeof($paItem) > 0) {
					$paItemLink = self::PA_BASE_URL.'/handle/'.$paItem[0]['handle'];
				}
				$aamItems[] = [
					'title' => $submission->getTitle($submission->getLocale()),
					'authors' => $submission->getAuthorString(),
					'linkToPsychArchives' => isset($paItemLink) ? $paItemLink : null,
				];
			}
		}
		$templateMgr->assign('aamItems', $aamItems);

		return $templateMgr->display($plugin->getTemplateResource('aamList.tpl'));
	}

	/**
	 * @param $context
	 * @return string|null
	 */
	private function _buildDoiPrefix($context)
	{
		$pubIdPrefix = null;
		$contextId = ($context == null) ? 0 : $context->getId();
		$doiPlugin = PluginRegistry::loadPlugin('pubIds', 'doi', $contextId);
		if (isset($doiPlugin) && $doiPlugin->getEnabled()) {
			$doiJournalPrefix = $doiPlugin->getSetting($contextId, 'doiPrefix');
			if (isset($doiJournalPrefix)) {
				$contextAcronym = PKPString::regexp_replace('/[^A-Za-z0-9]/', '', PKPString::strtolower($context->getAcronym($context->getPrimaryLocale())));
				$pubIdPrefix = self::DOI_PREFIX.$doiJournalPrefix.'/'.$contextAcronym.'.';
			}
		}
		return $pubIdPrefix;
	}


	/**
	 * @param $relation
	 * @return mixed|null
	 */
	private function _searchPsychArchivesItemByIsVersionOf($relation)
	{
		try {
			$curl = curl_init();
			curl_setopt_array(
				$curl,
				array(
					CURLOPT_URL => self::PA_BASE_URL."/rest/items/find-by-metadata-field",
					CURLOPT_RETURNTRANSFER => true,
					CURLOPT_HTTPHEADER => array('Accept: application/json', 'Content-Type: application/json'),
					CURLOPT_POST => true,
					CURLOPT_POSTFIELDS => '{"key": "dc.relation.isversionof","value": "'.$relation.'","language":null}',
				)
			);
			$result = curl_exec($curl);
			curl_close($curl);
			$itm = json_decode($result, true);
		} catch (Exception $e) {
			$itm = null;
		}

		return $itm;
	}
}
