<?php
class IndexController extends Controllers_Abstract
{
   public function indexAction(){
       echo 'dw1a';die;
       $this->getView()->assign("content", "Hello World");
       $this->redirect('/home/index/index');
   }
   
   public function loginAction()
   {
       header("location:/home/login/index");
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
		$mapper = \Admin\Mapper\AdminModel::getInstance();
		$result = $mapper->login($params);
		$this->response($result);
   }
   
   public function logoutAction()
   {
        
   }
}
?>

