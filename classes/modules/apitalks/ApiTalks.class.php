<?php
/*---------------------------------------------------------------------------
 * @Project: Alto CMS
 * @Project URI: http://altocms.com
 * @Description: Advanced Community Engine
 * @Copyright: Alto CMS Team
 * @License: GNU GPL v2 & MIT
 *----------------------------------------------------------------------------
 *
 * @package plugin AltoApi
 */

/**
 * Class PluginAltoApi_ModuleApi
 *
 */
class PluginAltoApi_ModuleApiTalks extends Module {

    public function Init() {

        return true;
    }

    /**
     * @param int|ModuleTalk_EntityTalk $xTalk
     *
     * @return array
     */
    public function getInfo($xTalk) {

        /** @var ModuleTalk_EntityTalk $oTalk */
        if (!is_object($xTalk)) {
            $oTalk = E::ModuleTalk()->GetTalkById(intval($xTalk));
        } else {
            $oTalk = $xTalk;
        }
        if (!$oTalk) {
            return array();
        }
        if ($oTalk->getUserId() != E::UserId()) {
            if ($oTalk->getTalkUsers()) {
                foreach($oTalk->getTalkUsers() as $oTalkUser) {
                    if ($oTalkUser->getUserId() == E::UserId()) {
                        return $oTalk->getApiData();
                    }
                }
            }
            return array();
        }
        return $oTalk->getApiData();
    }

    /**
     * @param int $iPageNum
     * @param int $iPageSize
     *
     * @return array
     */
    public function getList($iPageNum, $iPageSize) {

        $aFilter = array(
            'user_id' => E::UserId(),
        );
        $aTalks = E::ModuleTalk()->GetTalksByFilter($aFilter, $iPageNum, $iPageSize);

        $aResult = array(
            'total' => $aTalks['count'],
            'collection' => array(),
        );
        /** @var PluginAltoApi_ModuleApiTalks_EntityTalk $oTalk */
        foreach($aTalks['collection'] as $oTalk) {
            $aResult['collection'][] = $oTalk->getApiData();
        }

        return $aResult;
    }

}

// EOF