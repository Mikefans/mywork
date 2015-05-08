<?php
/**
 * 视图父类
 * @author yuyang
 *
 */
namespace Hlg;

class View extends \Yaf\View\Simple
{

    protected $_layout = null;

    protected $_theme;

    protected $_layoutVars = array();
    
    /**
     * 设置主题
     */
    public function setTheme($theme)
    {
        $this->_theme = $theme;
        $module = \Yaf\Dispatcher::getInstance()->getRequest()->getModuleName();
        $this->setScriptPath($this->getViewPath() . DS . lcfirst($module));
    }
    
    /**
     * 获取view所在路径
     */
    public function getViewPath(){
        if(!$this->_theme){
            $this->_theme = \Yaf\Registry::get("appConfig")->application->view->theme;
        }
        $viewPath = \Yaf\Registry::get("appConfig")->application->view->path;
        return $viewPath . DS . $this->_theme;
    }

    /**
     * 设置layout
     */
    public function setLayout($layout)
    {
        $this->_layout = $layout;
    }

    /**
     * 设置layout参数
     */
    public function assignLayout($name, $value = null)
    {
        if (is_array($name)) {
            $this->_layoutVars = array_merge($this->_layoutVars, $name);
        } else {
            $this->_layoutVars[$name] = $value;
        }
    }

    /**
     * 响应模板输出
     */
    public function render($tpl, $tplVars = NULL)
    {
        $content = parent::render($tpl, $tplVars);
        if ($this->_layout) {
            return $this->_renderLayout($content);
        } else {
            return $content;
        }
    }

    /**
     * 获取并返回响应view模板输出
     */
    public function renderView($view)
    {
        $viewInstance = $this->_renderView($view);
        return $viewInstance->render($viewInstance->getViewTemplet());
    }

    /**
     * 获取并输出响应view模板
     *
     * @param unknown $view            
     */
    public function displayView($view)
    {
        $viewInstance = $this->_renderView($view);
        $viewInstance->display($viewInstance->getViewTemplet());
    }

    /**
     * 获取响应的templet
     */
    public function getViewTemplet()
    {
        return false;
    }

    protected function _renderView($view)
    {
        $viewPath = str_replace('\\', DIRECTORY_SEPARATOR, $view) . ".php";
        \Yaf\Loader::import(APP_PATH . DS . "views" . DS . $viewPath);
        $class = "\\" . $view . "View";
        $viewInstance = new $class($this->getViewPath());
        $viewInstance->init();
        return $viewInstance;
    }

    /**
     * 响应layout模板输出
     */
    protected function _renderLayout($content)
    {
        $layout = $this->_initLayout();
        $layout->assign($this->_layoutVars);
        $layout->content = $content;
        return $layout->render($this->_layout . ".phtml");
    }

    /**
     * 初始化layout
     */
    protected function _initLayout()
    {
        $layout = new static($this->getViewPath());
        return $layout;
    }
}
