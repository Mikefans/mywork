<?php

class AuthPlugin extends Yaf_Plugin_Abstract
{

    /**
     * 是否要进行授权检测
     *
     * @var unknown
     */
    protected $_authCheck = true;

    /**
     * 不需要授权检测的路由
     *
     * @var unknown
     */
    protected $_noAuthRoute = array(
        "login"
    );

    public function routerShutdown(Yaf_Request_Abstract $request, Yaf_Response_Abstract $response)
    {
        $route = Yaf_Dispatcher::getInstance()->getRouter()->getCurrentRoute();
        if (in_array($route, $this->_noAuthRoute)) {
            $this->_authCheck = false;
        }
    }

    public function preDispatch(Yaf_Request_Abstract $request, Yaf_Response_Abstract $response)
    {
        if (! $this->_authCheck) {
            return true;
        }
        $isLoggedId = false;
        $session = Yaf_Session::getInstance();
        if (isset($session->m_user) && ! empty($session->m_user)) {
            return true;
        }
        if (! $isLoggedId) {
//             $request->setModuleName("Index");
//             $request->setControllerName("Index");
//             $request->setActionName("loginindex");
        }
    }
}
?>