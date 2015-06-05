<?php

class AuthPlugin extends Yaf\Plugin_Abstract
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
        "Login",
        "Verify",
        "Item",
        "Index",
        "Home",
    );

    public function routerShutdown(Yaf\Request_Abstract $request, Yaf\Response_Abstract $response)
    {
        $route =$request->getModuleName();// Yaf\Dispatcher::getInstance()->getRouter();
        if (in_array($route, $this->_noAuthRoute)) {
            $this->_authCheck = false;
        }
    }

    public function preDispatch(Yaf\Request_Abstract $request, Yaf\Response_Abstract $response)
    {
        if (! $this->_authCheck) {
            return true;
        }
        $isLoggedId = false;
        $session = \Yaf\Session::getInstance();
        if (isset($session->m_user) && ! empty($session->m_user)) {
            return true;
        }
        if (! $isLoggedId) {
            \Core::setError('请先登录，亲');
//             $request->setModuleName("Verify");
//             $request->setControllerName("Public");
//             $request->setActionName("login");
        }
    }
}
?>