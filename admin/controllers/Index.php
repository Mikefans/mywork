<?php
class IndexController extends Controllers_Abstract
{
   public function indexAction()
   {
       $this->disableView();
       $this->redirect('/home/index/index');
   }
   
   public function loginAction()
   {
       $params = $this->getParams();
        $this->disableView();
        $params = $this->getParams();
        $session = \Yaf\Session::getInstance();
        $code = $session->login_code;
        if (empty($code)){
            \Core::setError('请刷新验证码');
        }
        if ($code != $params['verify_code']){
            $session->del('login_code');
            \Core::setError('验证码错误');
        }
		$mapper = \Admin\AdminModel::getInstance();
		$result = $mapper->login($params);
		$this->response(array('msg' => 'success'));
   }
   
   public function logoutAction()
   {
       $session = \Yaf\Session::getInstance();
       $session->del('m_admin');
       $this->redirect('/index/logins');
   }
   
   public function errorAction()
   {
       $this->disableView();
   	   $msg = $this->getParam('msg','');
   	   $this->errorResponse($msg);
   }
   
   public function loginsAction()
   {}
}
?>

