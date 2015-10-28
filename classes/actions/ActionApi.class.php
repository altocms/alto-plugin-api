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
 * Class PluginAltoApi_ActionApi
 *
 */
class PluginAltoApi_ActionApi extends PluginAltoApi_Inherit_ActionApi {

    const ALTO_APP_KEY = 'X-Alto-Application-Key';
    const ALTO_AUTH_TOKEN = 'X-Alto-Auth-Token';
    const ALTO_API_CRYPT = 'X-Alto-Api-Crypt';

    const MAX_PAGE_SIZE = 25;

    protected $aAvailableMethods = array('GET', 'POST', 'PUT', 'DELETE');

    protected $sDefaultMethod = 'GET';

    protected $aResources = array();

    protected $bRestApi = false;

    protected $aResponseData = array();

    protected $iResponseError = 0;

    protected $sResponseMessage = '';

    protected $aApiApplicationData;

    /**
     * @return array
     */
    protected function _getAvailableMethods() {

        return $this->aAvailableMethods;
    }

    /**
     * @return string
     */
    protected function _getDefaultMethod() {

        return $this->sDefaultMethod;
    }

    /**
     * @param $sUriTemplate
     *
     * @return array
     */
    protected function _parseUriPattern($sUriTemplate) {

        $bOptional = null;
        if (($iPos = strpos($sUriTemplate, '/(')) && substr($sUriTemplate, -1) == ')') {
            $sUriTemplate = substr($sUriTemplate, 0, strlen($sUriTemplate) - 1);
            $bOptional = false;
        }
        $aRequiredUri = explode('/', trim($sUriTemplate, '/'));
        $aRequestParts = array();
        foreach($aRequiredUri as $iIndex => $sPart) {
            $bRegexp = false;
            $sType = '';
            $sName = null;
            if ($sPart[0] == '(' && $bOptional === false) {
                $bOptional = true;
                $sPart = substr($sPart, 1);
            }
            if ($sPart[0] == ':') {
                $sName = substr($sPart, 1);
                $sPart = '*';
            } elseif (preg_match('/([^?:]*)(\?.)?(:\w+)?/', $sPart, $aMatches)) {
                $sPart = $aMatches[1];
                $sType = (isset($aMatches[2]) ? $aMatches[2] : '');
                $sName = (isset($aMatches[3]) ? $aMatches[3] : '');
                if ($sType && $sType[0] == '?') {
                    $sType = substr($sType, 1);
                }
            }
            if ($sPart[0] == '[' && substr($sPart, -1) == ']') {
                $sPart = substr($sPart, 1, strlen($sPart) - 2);
                $bRegexp = true;
            }
            $aValue = array(
                'value' => $sPart,
                'type' => $sType,
                'name' => $sName,
                'optional' => $bOptional,
                'regexp' => $bRegexp,
            );
            $aRequestParts[] = $aValue;
            if ($sName) {
                $aRequestParts[$sName] = $aValue;
            }
        }

        return $aRequestParts;
    }

    /**
     * @param      $xRequestMethod
     * @param      $sRequestUri
     * @param null $sEventFunction
     *
     * @throws Exception
     */
    protected function AddEventUri($xRequestMethod, $sRequestUri, $sEventFunction = null) {

        if (func_num_args() < 3) {
            $sEventFunction = $sRequestUri;
            $sRequestUri = $xRequestMethod;
            $xRequestMethod = $this->_getDefaultMethod();
        } elseif(is_array($xRequestMethod)) {
            $xRequestMethod = array_map('strtoupper', $xRequestMethod);
        } else {
            $xRequestMethod = strtoupper($xRequestMethod);
        }
        if ($this->_isAvailableMethod($xRequestMethod)) {
            $aRequestParts = $this->_parseUriPattern($sRequestUri);
            $aEventArgs = array();
            $bRegexp = false;
            foreach($aRequestParts as $iIndex => $aValue) {
                if (is_numeric($iIndex)) {
                    if ($aValue['regexp']) {
                        if (!$aValue['optional']) {
                            $aEventArgs[] = $aValue['value'];
                            $bRegexp = true;
                        }
                    } else {
                        if (!$aValue['optional']) {
                            $aEventArgs[] = $aValue['value'];
                        }
                    }
                    //$aEventArgs[] = $sValue;
                }
            }
            $sResource = reset($aEventArgs);
            $sMethod = join('/', $aEventArgs);
            if (is_array($xRequestMethod)) {
                foreach($xRequestMethod as $sRequestMethod) {
                    $this->aResources[$sRequestMethod][$sResource][$sMethod] = $sEventFunction;
                }
            } else {
                $this->aResources[$xRequestMethod][$sResource][$sMethod] = $sEventFunction;
            }

            $aEventArgs[] = $sEventFunction;
            if ($bRegexp) {
                $this->_addEventHandler($aEventArgs, self::MATCH_TYPE_REG);
            } else {
                $this->_addEventHandler($aEventArgs, self::MATCH_TYPE_STR);
            }
        }
    }

    /**
     * @param string|array  $xRequestMethod
     *
     * @return bool
     */
    private function _isAvailableMethod($xRequestMethod) {

        $sRequestMethod = (isset($_SERVER['REQUEST_METHOD']) ? strtoupper($_SERVER['REQUEST_METHOD']) : '');

        if ($xRequestMethod === 'ANY') {
            return true;
        }
        if (!in_array($sRequestMethod, $this->_getAvailableMethods())) {
            return false;
        }
        if (is_array($xRequestMethod)) {
            return in_array($sRequestMethod, $xRequestMethod);
        }

        return $sRequestMethod === $xRequestMethod;

    }


    protected function _getApiApplicationData() {

        if (is_null($this->aApiApplicationData)) {
            if ($aApplications = C::Get('module.api.applications')) {
                $sAppKey = $this->_getRequestData('HEADER', self::ALTO_APP_KEY);
                if (empty($sAppKey) || empty($aApplications[$sAppKey])) {
                    $this->aApiApplicationData = false;
                } else {
                    $this->aApiApplicationData = $aApplications[$sAppKey];
                }
            } else {
                $this->aApiApplicationData = array();
            }
        }
        return $this->aApiApplicationData;
    }

    /**
     * @param string $sBodyData
     *
     * @return array
     */
    protected function _prepareRequestBody($sBodyData) {

        $aResult = array();
        if ($sBodyData) {
            if ($this->_getRequestData('HEADER', 'Content-Transfer-Encoding') == self::ALTO_API_CRYPT) {
                $aApiApplicationData = $this->_getApiApplicationData();
                if (!empty($aApiApplicationData['secret_key'])) {
                    F::Xxtea_Decode($sBodyData, $aApiApplicationData['secret_key']);
                }
            }
            if ($this->_getRequestData('HEADER', 'Content-Type') == 'application/json') {
                $aResult = json_decode($sBodyData, true, 512, JSON_BIGINT_AS_STRING);
            } else {
                $aResult = $this->_prepareRequestBody($sBodyData);
            }
        }
        return $aResult;
    }

    /**
     * @param string $sName
     *
     * @return mixed
     */
    protected function _getParam($sName) {

        $sType = $this->_getRequestMethod();
        if ($sType = 'POST') {
            $sContentType = $this->_getRequestData('HEADER', 'Content-Type');
            if ($sContentType == 'application/x-www-form-urlencoded' || $sContentType == 'multipart/form-data') {
                $xResult = $this->_getRequestData('POST', $sName);
            } else {
                $xResult = $this->_getRequestData('BODY', $sName);
            }
        } else {
            $xResult = $this->_getRequestData($sType, $sName);
        }
        return $xResult;
    }


    protected function _setResponse() {

        // Устанавливаем формат Ajax ответа
        E::ModuleViewer()->SetResponseAjax('json', true, false);
        R::SetAutoDisplay(false);
        $this->bRestApi = true;
    }

    /**
     * Events
     */
    protected function RegisterEvent() {

        parent::RegisterEvent();

        // /api/users
        $this->AddEventUri('POST', '/users/login', 'EventPostUsersLogin');
        $this->AddEventUri('POST', '/users/authorization', 'EventPostUsersAuthorization');

        $this->AddEventUri('GET', '/users/list', 'EventGetUsersList');
        $this->AddEventUri('GET', '/users/me', 'EventGetUsersMe');
        $this->AddEventUri('GET', '/users', 'EventGetUsers');

        // /api/posts
        $this->AddEventUri('GET', '/posts/list', 'EventGetPostsList');
        $this->AddEventUri('GET', '/posts', 'EventGetPosts');

        // /api/blogs
        $this->AddEventUri('GET', '/blogs/list', 'EventGetBlogsList');
        $this->AddEventUri('GET', '/blogs', 'EventGetBlogs');

        // /api/talks
        $this->AddEventUri('GET', '/talks/list', 'EventGetTalksList');
        $this->AddEventUri('GET', '/talks', 'EventGetTalks');
    }


    /**
     * @param string $sEvent
     *
     * @return bool
     */
    public function Access($sEvent) {

        $bResult = parent::Access($sEvent);
        if ($bResult && $this->bRestApi) {
            if ($this->_getApiApplicationData() === false) {
                return false;
            }
            $aUriParams = R::GetParams();
            if ($aUriParams) {
                $sUri = $sEvent . '/' . join('/', $aUriParams);
            } else {
                $sUri = $sEvent;
            }
        }
        return $bResult;
    }


    public function Init() {

        parent::Init();
        $sRequestMethod = $this->_getRequestMethod();
        $sResource = R::GetActionEvent();
        $aUriParams = R::GetParams();
        if ($aUriParams) {
            $sUri = $sResource . '/' . join('/', $aUriParams);
        } else {
            $sUri = $sResource;
        }
        if (isset($this->aResources[$sRequestMethod][$sResource][$sUri])) {
            $this->bRestApi = true;
        }
    }

    public function GetTemplate() {

        if ($this->bRestApi) {
            return null;
        }
        return parent::GetTemplate();
    }

    public function EventError() {

        if (!($aError = E::ModuleApi()->GetLastError())) {
            $aError = E::ModuleApi()->ERROR_CODE_9002;
        }

        parent::EventError();

        if ($this->bRestApi) {
            $this->iResponseError = $aError['code'];
            $this->sResponseMessage = $aError['description'];
        }
    }

    /**
     * @param array $aUserAuthData
     *
     * @return ModuleUser_EntityUser
     */
    protected function _userLogin($aUserAuthData) {

        // Seek user by mail or by login
        /** @var ModuleUser_EntityUser $oUser */
        $oUser = E::ModuleUser()->GetUserAuthorization($aUserAuthData);
        if ($oUser) {
            if ($this->iResponseError) {
                switch($this->iResponseError) {
                    case ModuleUser::USER_AUTH_ERR_NOT_ACTIVATED:
                        $this->sResponseMessage = E::ModuleLang()->Get(
                            'user_not_activated',
                            array('reactivation_path' => R::GetPath('login') . 'reactivation')
                        );
                        break;
                    case ModuleUser::USER_AUTH_ERR_IP_BANNED:
                        $this->sResponseMessage = E::ModuleLang()->Get('user_ip_banned');
                        break;
                    case ModuleUser::USER_AUTH_ERR_BANNED_DATE:
                        $this->sResponseMessage = E::ModuleLang()->Get('user_banned_before', array('date' => $oUser->GetBanLine()));
                        break;
                    case ModuleUser::USER_AUTH_ERR_BANNED_UNLIM:
                        $this->sResponseMessage = E::ModuleLang()->Get('user_banned_unlim');
                        break;
                    default:
                        $this->sResponseMessage = E::ModuleLang()->Get('user_login_bad');
                }
            } else {
                // Авторизуем
                E::ModuleUser()->Authorization($oUser, true);
            }
        } else {
            $this->sResponseMessage = E::ModuleLang()->Get('user_login_bad');
        }
        return $oUser;
    }

    /**
     * @return string
     */
    protected function _getAuthToken() {

        $sAuthToken = $this->_getRequestData('HEADER', self::ALTO_AUTH_TOKEN);
        if (!$sAuthToken) {
            $sAuthToken = $this->_getRequestData('BODY'. 'auth_token');
        }
        if (!$sAuthToken) {
            $sAuthToken = $this->_getRequestData('GET'. 'auth_token');
        }
        return $sAuthToken;
    }

    /**
     * @param bool $bOptionalAuthorization
     *
     * @return ModuleUser_EntityUser|null
     */
    protected function _getAuthorizedUser($bOptionalAuthorization) {

        $oUser = null;
        $sAuthToken = $this->_getAuthToken();
        if ($sAuthToken) {
            $aUserAuthData = array(
                'session' => str_replace('-', ':', $sAuthToken),
                'error' => &$this->iResponseError,
            );

            /** @var ModuleUser_EntityUser $oUser */
            $oUser = $this->_userLogin($aUserAuthData);
        }
        if (!$oUser && !$bOptionalAuthorization) {
            $this->iResponseError = ModuleUser::USER_AUTH_ERROR;
            $this->sResponseMessage = E::ModuleLang()->Get('user_login_bad');
        }

        return $oUser;
    }

    protected function _getPage() {

        $iPageNum = intval($this->_getRequestData('GET', 'page'));
        $iPageSize = intval($this->_getRequestData('GET', 'page_size'));

        if ($iPageNum < 1) {
            $iPageNum = 1;
        }
        if ($iPageSize < 1) {
            $iPageSize = self::MAX_PAGE_SIZE;
        }

        return array($iPageNum, $iPageSize);
    }

    /* *************** users *************** */

    /**
     * POST /users/login
     */
    public function EventPostUsersLogin() {

        $this->_setResponse();

        // Проверяем передачу логина пароля через POST
        $sUserLogin = trim($this->_getParam('login'));
        $sUserEmail = trim($this->_getParam('email'));
        $sUserPassword = $this->_getParam('password');

        if ((!$sUserLogin && !$sUserEmail) || !trim($sUserPassword)) {
            $this->iResponseError = ModuleUser::USER_AUTH_ERROR;
            $this->sResponseMessage = E::ModuleLang()->Get('user_login_bad');
            $oUser = null;
        } else {
            $aUserAuthData = array(
                'login' => $sUserLogin,
                'email' => $sUserEmail,
                'password' => $sUserPassword,
                'error' => &$this->iResponseError,
            );
            /** @var ModuleUser_EntityUser $oUser */
            $oUser = $this->_userLogin($aUserAuthData);
        }

        if ($oUser) {
            $this->aResponseData['user'] = E::ModuleApiUsers()->getInfo($oUser);
        }
    }

    /**
     * POST /users/authorization
     */
    public function EventPostUsersAuthorization() {

        $this->_setResponse();
        $oUser = $this->_getAuthorizedUser(true);

        if ($oUser) {
            $this->aResponseData['user'] = E::ModuleApiUsers()->getInfo($oUser);
        }
    }

    /**
     * GET /users/list
     */
    public function EventGetUsersList() {

        $this->_setResponse();
        list($iPageNum, $iPageSize) = $this->_getPage();

        $this->aResponseData['users'] = E::ModuleApiUsers()->getList($iPageNum, $iPageSize);
    }

    /**
     * GET /users/me
     */
    public function EventGetUsersMe() {

        $this->_setResponse();
        $oUser = $this->_getAuthorizedUser(true);

        $this->aResponseData['user'] = E::ModuleApiUsers()->getInfo($oUser);
    }

    /**
     * GET /users/:id
     */
    public function EventGetUsers() {

        $this->_setResponse();

        $iUserId = intval($this->GetParam(0));
        if (!$iUserId) {
            $this->_Error(E::ModuleApi()->ERROR_CODE_9005);
            return;
        }
        $this->aResponseData['user'] = E::ModuleApiUsers()->getInfo($iUserId);
    }

    /* *************** posts *************** */

    /**
     * GET /posts/list
     */
    public function EventGetPostsList() {

        $this->_setResponse();
        list($iPageNum, $iPageSize) = $this->_getPage();

        $this->aResponseData['posts'] = E::ModuleApiPosts()->getList($iPageNum, $iPageSize);
    }

    /**
     * GET /posts/:id
     * GET /posts/:id/comments
     */
    public function EventGetPosts() {

        $this->_setResponse();
        list($iPageNum, $iPageSize) = $this->_getPage();

        $iPostId = intval($this->GetParam(0));
        if (!$iPostId) {
            $this->_Error(E::ModuleApi()->ERROR_CODE_9005);
            return;
        }

        $sCmd = $this->GetParam(1);
        if (empty($sCmd)) {
            $this->aResponseData['post'] = E::ModuleApiPosts()->getInfo($iPostId);
        } elseif ($sCmd == 'comments') {
            $aPost = E::ModuleApiPosts()->getInfo($iPostId);
            $this->aResponseData['post'] = $aPost;
            if ($aPost) {
                $this->aResponseData['comments'] = E::ModuleApiPosts()->getComments($iPostId, $iPageNum, $iPageSize);
            }
        }
    }

    /* *************** blogs *************** */

    /**
     * GET /blogs/list
     */
    public function EventGetBlogsList() {

        $this->_setResponse();
        list($iPageNum, $iPageSize) = $this->_getPage();

        $this->aResponseData['blogs'] = E::ModuleApiBlogs()->getList($iPageNum, $iPageSize);
    }

    /**
     * GET /blogs/:id
     * GET /blogs/:id/posts
     */
    public function EventGetBlogs() {

        $this->_setResponse();
        list($iPageNum, $iPageSize) = $this->_getPage();

        $iBlogId = intval($this->GetParam(0));
        if (!$iBlogId) {
            $this->_Error(E::ModuleApi()->ERROR_CODE_9005);
            return;
        }
        $sCmd = $this->GetParam(1);
        if (empty($sCmd)) {
            // :id
            $this->aResponseData['blog'] = E::ModuleApiBlogs()->getInfo($iBlogId);
        } elseif ($sCmd == 'posts') {
            // :id/posts
            $aBlog = E::ModuleApiBlogs()->getInfo($iBlogId);
            $this->aResponseData['blog'] = $aBlog;
            if ($aBlog) {
                $this->aResponseData['posts'] = E::ModuleApiBlogs()->getPosts($iBlogId, $iPageNum, $iPageSize);
            }
        }
    }

    /* *************** talks *************** */

    /**
     * GET /talks/list
     */
    public function EventGetTalksList() {

        $this->_setResponse();
        $this->_getAuthorizedUser(true);
        list($iPageNum, $iPageSize) = $this->_getPage();

        $this->aResponseData = E::ModuleApiTalks()->getList($iPageNum, $iPageSize);
    }

    /**
     * GET /talks/:id
     */
    public function EventGetTalks() {

        $this->_setResponse();
        $this->_getAuthorizedUser(true);

        $iTalkId = intval($this->GetParam(0));
        if (!$iTalkId) {
            $this->_Error(E::ModuleApi()->ERROR_CODE_9005);
            return;
        }
        $oTalk = E::ModuleApiTalks()->getInfo($iTalkId);
        if (!$oTalk) {
            $this->_Error(E::ModuleApi()->ERROR_CODE_9003);
        } else {
            $this->aResponseData['talk'] = $oTalk;
        }
    }

    /* *************** ****** *************** */

    /**
     * Shutdown and output result
     */
    public function EventShutdown() {

        parent::EventShutdown();
        if ($this->bRestApi) {
            if (E::IsUser()) {
                $this->aResponseData['auth_token'] = E::User()->getAuthToken();
            }

            E::ModuleViewer()->AssignAjax('error', $this->iResponseError);
            E::ModuleViewer()->AssignAjax('message', $this->sResponseMessage);
            E::ModuleViewer()->AssignAjax('data', $this->aResponseData);
            E::ModuleViewer()->SetResponseHeader('Content-type', 'application/json; charset=utf-8');
            $sOutput = E::ModuleViewer()->getAjaxVars();
            E::ModuleViewer()->Flush($sOutput);
        }
    }

}

// EOF