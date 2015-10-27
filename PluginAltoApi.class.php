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
 * Запрещаем напрямую через браузер обращение к этому файлу.
 */
if (!class_exists('Plugin')) {
    die('Hacking attempt!');
}

/**
 * @package plugin Counters
 */
class PluginAltoApi extends Plugin {

    protected $aDelegates = array(
    );

    protected $aInherits = array(
        'action' => array(
            'ActionApi',
        ),
        'module' => array(
            'ModuleApiUsers',
            'ModuleApiPosts',
            'ModuleApiBlogs',
            'ModuleApiTalks',
        ),
        'entity' => array(
            'ModuleUser_EntityUser' => '_ModuleApiUsers_EntityUser',
            'ModuleTopic_EntityTopic' => '_ModuleApiPosts_EntityPost',
            'ModuleBlog_EntityBlog' => '_ModuleApiBlogs_EntityBlog',
            'ModuleTalk_EntityTalk' => '_ModuleApiTalks_EntityTalk',
            'ModuleComment_EntityComment' => '_ModuleApiComments_EntityComment',
        ),
    );

    /**
     * Активация плагина
     */
    public function Activate() {

        $sVersion = $this->ReadStorageVersion();
        if (!$sVersion) {
            if (!$this->isFieldExists('?_user', 'user_api_key')) {
                $this->ExportSQL(__DIR__ . '/install/db/init.sql');
            }
        }

        return true;
    }

    /**
     * Инициализация плагина
     */
    public function Init() {

        return true;
    }
}

// EOF