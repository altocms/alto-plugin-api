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
class PluginAltoApi_ModuleApiUsers extends Module {

    public function Init() {

        return true;
    }

    /**
     * @param int|ModuleUser_EntityUser $xUser
     *
     * @return array
     */
    public function getInfo($xUser) {

        /** @var PluginAltoApi_ModuleApiUsers_EntityUser $oUser */
        if (!is_object($xUser)) {
            $oUser = E::ModuleUser()->GetUserById(intval($xUser));
        } else {
            $oUser = $xUser;
        }
        if (!$oUser) {
            return array();
        }
        return $oUser->getApiData();
    }

    /**
     * @param int $iPageNum
     * @param int $iPageSize
     *
     * @return array
     */
    public function getList($iPageNum, $iPageSize) {

        $aFilter = array('session.session_exit' => false);
        $aOrder = array('session.session_date_last' => 'desc');
        $aUsers = E::ModuleUser()->GetUsersByFilter($aFilter, $aOrder, $iPageNum, $iPageSize);
        $aResult = array(
            'total' => $aUsers['count'],
            'collection' => array(),
        );
        /** @var PluginAltoApi_ModuleApiUsers_EntityUser $oUser */
        foreach($aUsers['collection'] as $oUser) {
            $aResult['collection'][] = $oUser->getApiData();
        }

        return $aResult;
    }

}

// EOF