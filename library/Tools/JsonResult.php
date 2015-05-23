<?php 
namespace Tools;
class JsonResult extends \Yaf\Controller_Abstract
{
    
	private $_result;
	
	public function _construct()
	{
		$this->_result = array(
			'status' => 'success',
		    'desc' =>'',
		    'result' =>''
		);
	}
	
	public function responseResult($res)
	{
	    $this->_result = array(
			'status' => 'success',
		    'result' =>$res
		);
	    $result = json_encode($this->_result, JSON_UNESCAPED_UNICODE);
		$this->_response($result);
	}
	
	public function errorResponse($msg)
	{
		$this->_result = array(
			'status' => 'error',
		    'desc' => $msg
		);
		$result = json_encode($this->_result, JSON_UNESCAPED_UNICODE);
		$this->_response($result);
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
}


?>