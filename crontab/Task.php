<?php 
error_reporting(E_ALL | E_STRICT);

define('DS', DIRECTORY_SEPARATOR);
define('PS', PATH_SEPARATOR);
define('BP', realpath(dirname(dirname(__DIR__)).DS));
define("APP_PATH",BP.DS."app");
define("APP_CONF_PATH",APP_PATH.DS."conf");

class Task{
    protected $_taskConfig = array();
    
    function __construct($taskType,$taskConfig = array()){
        $this->_taskConfig = $taskConfig;
        $this->_taskConfig['type'] = $taskType;
    }
    
    /**
     * 运行任务
     * @param unknown $taskController
     * @param unknown $taskName
     */
    public function run($taskController,$taskName){
        $actionName = $taskName.ucfirst($this->_taskConfig['type']);
        $taskFlag = $taskController.ucfirst($actionName).".flag";
        $this->_taskConfig['task_flag'] = $taskFlag;
        \Yaf\Registry::set("taskConfig",$this->_taskConfig);
        $request = $this->_getRequest("Task", ucfirst($taskController), $actionName);
        $app = new \Yaf\Application(APP_CONF_PATH.DS."app.ini");
        $app->bootstrap()->getDispatcher()->dispatch($request);
    }
    
    /**
     * 设置task对应的group_id
     * @param unknown $groupId
     */
    public function setGroupId($groupId){
        $this->_taskConfig['group_id'] = $groupId;
    }
    
    /**
     * 设置每次处理完一个task后sleep的时间
     * @param unknown $time
     */
    public function setTaskSleep($time){
        $this->_taskConfig['task_sleep'] = $time;
    }
    
    /**
     * 设置task对应的队列名称
     * @param unknown $queueName
     */
    public function setQueueName($queueName){
        $this->_taskConfig['queue_name'] = $queueName;
    }
    
    /**
     * 获取任务配置信息
     * @return multitype:
     */
    public function getConfig(){
        return $this->_taskConfig;
    }
    
    /**
     * 获取响应的request
     * @param unknown $module
     * @param unknown $controller
     * @param unknown $action
     */
    protected function _getRequest($module,$controller,$action){
        $request = new \Yaf\Request\Simple();
        $request->setModuleName($module);
        $request->setControllerName($controller);
        $request->setActionName($action);
        return $request;
    }
}
?>
