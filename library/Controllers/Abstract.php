<?php
class Controllers_Abstract extends Yaf\Controller_Abstract
{

    protected $_params = array();

    public function init()
    {
        $this->_filterRequestParams();
        if ($this->getRequest()->isXmlHttpRequest()) {
            $this->disableView();
        } else {
           // $this->getView()->setTheme(\Yaf\Registry::get("appConfig")->application->view->theme);
        }
    }

    /**
     * 禁用view自动响应
     */
    public function disableView()
    {
        \Yaf\Dispatcher::getInstance()->disableView();
    }

    /**
     * 获取参数
     *
     * @param unknown $key            
     * @param string $value            
     * @return multitype: string
     */
    public function getParam($key, $value = null)
    {
        if (isset($this->_params[$key])) {
            return $this->_params[$key];
        }
        return $value;
    }

    /**
     * 获取所有参数
     */
    public function getParams()
    {
        return $this->_params;
    }

    /**
     * 输出json格式响应
     *
     * @param unknown $body            
     */
    protected function _response($body)
    {
        if (! $this->getRequest()->isCli()) {
            $this->getResponse()->setHeader("Content-Type", "application/json;charset=utf-8");
        }
        $this->getResponse()->response();
        $this->getResponse()->setBody($body);
    }

    /**
     * xss 过滤
     */
    protected function _filterRequestParams()
    {
        $filterMethod = array(
            "POST",
            "PUT",
            "DELETE"
        );
        if (in_array($this->getRequest()->getMethod(), $filterMethod)) {
            $inputData = json_decode(file_get_contents("php://input", "r"), true);
        }
        $request = $_REQUEST;
        if(isset($inputData) && $inputData){
            $request = array_merge($request,$inputData);
        }
        $params = $this->getRequest()->getParams();
        $this->_params = $this->_filterXss(array_merge($request, $params));
    }

    /**
     * 过滤xss
     *
     * @param unknown $array            
     * @param string $topLevel            
     * @return multitype:Ambigous <string, multitype:string >
     */
    protected function _filterXss($array, $topLevel = true)
    {
        $newArray = array();
        foreach ($array as $key => $value) {
            if (! $topLevel) {
                $newKey = stripslashes($key);
                if ($newKey !== $key) {
                    unset($array[$key]);
                }
                $key = $newKey;
            }
            $newArray[$key] = is_array($value) ? $this->_filterXss($value, false) : stripslashes($value);
        }
        return $newArray;
    }
}
