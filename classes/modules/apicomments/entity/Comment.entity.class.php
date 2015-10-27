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
 * Class PluginAltoApi_ModuleApiComments_EntityComment
 *
 * @mixin ModuleComment_EntityComment
 */
class PluginAltoApi_ModuleApiComments_EntityComment extends PluginAltoApi_Inherits_ModuleComment_EntityComment {

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
                'text'      => $this->getText(),
                'date'      => $this->getDate(),
                'author'    => $this->getUser()->getApiData(),
            );
        }
        return $aData;
    }

}

// EOF