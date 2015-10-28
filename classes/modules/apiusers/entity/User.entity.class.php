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
 * Class PluginAltoApi_ModuleApiUsers_EntityUser
 *
 * @mixin ModuleUser_EntityUser
 */
class PluginAltoApi_ModuleApiUsers_EntityUser extends PluginAltoApi_Inherits_ModuleUser_EntityUser {

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
                'login'     => $this->getLogin(),
                'name'      => $this->getDisplayName(),
                'sex'       => $this->getProfileSex(),
                'avatar'    => $this->getAvatarUrl(),
                'photo'     => $this->getPhotoUrl(),
                'about'     => $this->getProfileAbout(),
                'birthday'  => $this->getProfileBirthday(),
                'skill'     => $this->getSkill(),
                'rating'    => $this->getRating(),
                'profile'   => $this->getProfileUrl(),
                'country'   => $this->getProfileCountry(),
                'city'      => $this->getProfileCity(),
                'region'    => $this->getProfileRegion(),
                'is_online' => $this->isOnline(),
                'is_friend' => $this->getUserIsFriend(),
            );
        }
        return $aData;
    }

    public function getAuthToken() {

        $sSessionKey = $this->getLastSession();
        if ($sSessionKey) {
            return str_replace(':', '-', $sSessionKey);
        }
        return null;
    }

}

// EOF