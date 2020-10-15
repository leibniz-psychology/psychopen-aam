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
                $aamItems[] = [
                    'title' => $submission->getTitle($submission->getLocale()),
                    'authors' => $submission->getAuthorString(),
                    'preliminaryDOI' => $pubIdPrefix.$submission->getId(),
                ];
            }
        }
        $templateMgr->assign('aamItems', $aamItems);
        return $templateMgr->display($plugin->getTemplateResource('aamList.tpl'));
    }

    /**
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
