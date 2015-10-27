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
class PluginAltoApi_ModuleApiPosts extends Module {

    public function Init() {

        return true;
    }

    /**
     * @param int|ModuleTopic_EntityTopic $xTopic
     *
     * @return array
     */
    public function getInfo($xTopic) {

        /** @var PluginAltoApi_ModuleApiPosts_EntityPost $oTopic */
        if (!is_object($xTopic)) {
            $oTopic = E::ModuleTopic()->GetTopicById(intval($xTopic));
        } else {
            $oTopic = $xTopic;
        }
        if (!$oTopic) {
            return array();
        }
        return $oTopic->getApiData();
    }

    /**
     * @param int $iPageNum
     * @param int $iPageSize
     *
     * @return array
     */
    public function getList($iPageNum, $iPageSize) {

        $aTopics = E::ModuleTopic()->GetTopicsNewAll($iPageNum, $iPageSize);
        $aResult = array(
            'total' => $aTopics['count'],
            'collection' => array(),
        );
        /** @var PluginAltoApi_ModuleApiPosts_EntityPost $oTopic */
        foreach($aTopics['collection'] as $oTopic) {
            $aResult['collection'][] = $oTopic->getApiData();
        }
        return $aResult;
    }

    public function getComments($iTopicId, $iPageNum, $iPageSize) {

        $sCacheKey = 'api_topic_' . $iTopicId;
        $oTopic = E::ModuleCache()->GetTmp($sCacheKey);
        if (!$oTopic) {
            $oTopic = E::ModuleTopic()->GetTopicById($iTopicId);
        }

        if (!$oTopic || !($oBlog = $oTopic->getBlog())) {
            return array();
        }

        $oBlogType = $oBlog->GetBlogType();
        if ($oBlogType) {
            $bCloseBlog = !$oBlog->CanReadBy(E::User());
        } else {
            // if blog type not defined then it' open blog
            $bCloseBlog = false;
        }

        if ($bCloseBlog) {
            return array();
        }

        $aComments = E::ModuleComment()->GetCommentsByTargetId($oTopic, 'topic', $iPageNum, $iPageSize);

        $aResult = array(
            'total' => $oTopic->getCountComment(),
            'collection' => array(),
        );
        foreach($aComments['comments'] as $oComment) {
            $aResult['collection'][] = $oComment->getApiData();
        }

        return $aResult;
    }

}

// EOF