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
class PluginAltoApi_ModuleApiBlogs extends Module {

    public function Init() {

        return true;
    }

    /**
     * @param int|ModuleBlog_EntityBlog $xBlog
     *
     * @return array
     */
    public function getInfo($xBlog) {

        /** @var ModuleBlog_EntityBlog $oBlog */
        if (!is_object($xBlog)) {
            $iBlogId = intval($xBlog);
            $sCacheKey = 'api_blog_' . $iBlogId;
            $oBlog = E::ModuleCache()->GetTmp($sCacheKey);
            if (!$oBlog) {
                $oBlog = E::ModuleBlog()->GetBlogById($iBlogId);
            }
        } else {
            $oBlog = $xBlog;
            $sCacheKey = 'api_blog_' . $oBlog->getid();
        }
        if (!$oBlog) {
            return array();
        }
        E::ModuleCache()->SetTmp($oBlog, $sCacheKey);

        return $oBlog->getApiData();
    }

    /**
     * @param int $iPageNum
     * @param int $iPageSize
     *
     * @return array
     */
    public function getList($iPageNum, $iPageSize) {

        // * Фильтр поиска блогов
        $aFilter = array(
            'include_type' => E::ModuleBlog()->GetAllowBlogTypes(E::User(), 'list', true),
            'order' => array('blog_title' => 'asc'),
        );
        // * Получаем список блогов
        $aBlogs = E::ModuleBlog()->GetBlogsByFilter(
            $aFilter,
            $iPageNum, $iPageSize
        );
        $aResult = array(
            'total' => $aBlogs['count'],
            'collection' => array(),
        );
        /** @var PluginAltoApi_ModuleApiBlogs_EntityBlog $oBlog */
        foreach($aBlogs['collection'] as $oBlog) {
            $aResult['collection'][] = $oBlog->getApiData();
        }

        return $aResult;
    }

    /**
     * @param int $iBlogId
     * @param int $iPageNum
     * @param int $iPageSize
     *
     * @return array
     */
    public function getPosts($iBlogId, $iPageNum, $iPageSize) {

        $sCacheKey = 'api_blog_' . $iBlogId;
        $oBlog = E::ModuleCache()->GetTmp($sCacheKey);
        if (!$oBlog) {
            $oBlog = E::ModuleBlog()->GetBlogById($iBlogId);
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

        $aTopics = E::ModuleTopic()->GetTopicsByBlog($oBlog, $iPageNum, $iPageSize, 'newall', null);

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

}

// EOF