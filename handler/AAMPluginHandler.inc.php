<?php

import('classes.handler.Handler');

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
 * @file plugins/generic/psychopen-aam/AAMPluginHandler.inc.php
 * @ingroup plugins_generic_aam
 * @brief Handler for Forthcoming Articles Plugin: shows aam page and has functions to receive PsychArchive Items vie REST API
 *
 */
class AAMPluginHandler extends Handler
{

	private const STATUS_QUEUED = 1;
	private const WORKFLOW_STAGE_ID_EDITING = 4;
	private const WORKFLOW_STAGE_ID_PRODUCTION = 5;
	private const DOI_PREFIX = 'https://doi.org/';
	private const PA_BASE_URL = 'https://www.psycharchives.org';

	/**
	 * This function displays the page that shows a list of all accepted but not yet published articles of a journal.
	 *
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
		$submissionsIterator = Services::get('submission')->getMany(
			[
				'contextId' => $contextId,
				'status' => self::STATUS_QUEUED,
				'stageIds' => [self::WORKFLOW_STAGE_ID_EDITING, self::WORKFLOW_STAGE_ID_PRODUCTION],
			]
		);
		$pubIdPrefix = $this->_buildDoiPrefix($context);
		foreach ($submissionsIterator as $submission) {
			$aamItems[] = [
				'title' => $submission->getTitle($submission->getLocale()),
				'authors' => $submission->getAuthorString(),
				'preliminaryDOI' => $pubIdPrefix.$submission->getId(),
			];
		}
		$templateMgr->assign('aamItems', $aamItems);

		return $templateMgr->display($plugin->getTemplateResource('aamList.tpl'));
	}

	/**
	 * This function is called via JavaScript to load all available items links from PsychArchives.
	 * If a link is availible, it will be shown besides the submission.
	 *
	 * @param $args
	 * @param $request
	 * @return mixed|null
	 */
	public function getPsychArchivesLink($args, $request)
	{
		if ($request->getUserVars() && sizeof($request->getUserVars()) > 0 && $request->getUserVars()['doi']) {
			$relation = $request->getUserVars()['doi'];
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
				if (isset($itm) && is_array($itm) && sizeof($itm) > 0) {
					$paLink = self::PA_BASE_URL.'/handle/'.$itm[0]['handle'];

					return new JSONMessage(true, ['paLink' => $paLink]);
				}
			} catch (Exception $e) {

			}
		}

		return new JSONMessage(false);
	}

	/**
	 * This function creates the DOI prefix for a submission, because at this time no DOI is provided by the OJS.
	 *
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


}
