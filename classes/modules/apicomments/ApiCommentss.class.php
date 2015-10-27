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
class PluginAltoApi_ModuleApiComments extends Module {

    public function Init() {

        return true;
    }

    /**
     * @param int|ModuleComment_EntityComment $xComment
     *
     * @return array
     */
    public function getInfo($xComment) {

        /** @var ModuleComment_EntityComment $oComment */
        if (!is_object($xComment)) {
            $iCommentId = intval($xComment);
            $sCacheKey = 'api_blog_' . $iCommentId;
            $oComment = E::ModuleCache()->GetTmp($sCacheKey);
            if (!$oComment) {
                $oComment = E::ModuleComment()->GetCommentById($iCommentId);
            }
        } else {
            $oComment = $xComment;
            $sCacheKey = 'api_blog_' . $oComment->getid();
        }
        if (!$oComment) {
            return array();
        }
        E::ModuleCache()->SetTmp($oComment, $sCacheKey);

        return $oComment->getApiData();
    }

}

// EOF