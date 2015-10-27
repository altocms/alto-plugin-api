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
 * Class PluginAltoApi_ModuleApiTalks_EntityTalk
 *
 * @mixin ModuleTalk_EntityTalk
 */
class PluginAltoApi_ModuleApiTalks_EntityTalk extends PluginAltoApi_Inherits_ModuleTalk_EntityTalk {

    public function getApiData($aProps = array()) {

        $aData = array();
        if (!empty($aProps)) {
            foreach($aProps as $sProp => $sKey) {
                if (is_numeric($sProp)) {
                    $sProp = $sKey;
                }
                $aData[$sKey] = $this->getProp($sProp);
            }
        } else {
            $aUsers = array();
            foreach($this->getTalkUsers() as $oTalkUser) {
                if ($oTalkUser->getUser()) {
                    $aUsers[] = $oTalkUser->getUser()->getApiData();
                }
            }
            $aData = array(
                'id'        => $this->getId(),
                'title'     => $this->getTitle(),
                'text'      => $this->getText(),
                'date'      => $this->getDate(),
                'author'    => $this->getUser()->getApiData(),
                'count_comments' => $this->getCountComment(),
                'users'     => $aUsers,
            );
        }
        return $aData;
    }

}

// EOF