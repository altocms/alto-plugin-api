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
 * Class PluginAltoApi_ModuleApiPosts_EntityPost
 *
 * @mixin ModuleTopic_EntityTopic
 */
class PluginAltoApi_ModuleApiPosts_EntityPost extends PluginAltoApi_Inherits_ModuleTopic_EntityTopic {

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
            $aData = array(
                'id'        => $this->getId(),
                'title'     => $this->getTitle(),
                'annotation'=> $this->getTextShort(),
                'text'      => $this->getText(),
                'date'      => $this->getDate(),
                'author'    => $this->getUser()->getApiData(),
                'count_comments' => $this->getCountComment(),
            );
        }
        return $aData;
    }

}

// EOF