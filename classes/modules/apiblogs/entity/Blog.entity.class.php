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
 * Class PluginAltoApi_ModuleApiBlogs_EntityBlog
 *
 * @mixin ModuleBlog_EntityBlog
 */
class PluginAltoApi_ModuleApiBlogs_EntityBlog extends PluginAltoApi_Inherits_ModuleBlog_EntityBlog {

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
                'annotation'=> $this->getDescription(),
                'date'      => $this->getDateAdd(),
                'avatar'   => $this->getAvatarUrl(),
                'author'    => $this->getOwner()->getApiData(),
                'count_topics' => $this->getCountTopic(),
                'new_topics' => E::ModuleTopic()->GetCountTopicsByBlogNew($this),
            );
        }
        return $aData;
    }

}

// EOF