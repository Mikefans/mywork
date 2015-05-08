<?php 
namespace Response;
class Response extends Controllers_Abstract
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
		$this->_response($result)
	}
	
	public function errorResponse($msg)
	{
		$this->_result = array(
			'status' => 'error',
		    'desc' => $msg
		);
		$result = json_encode($this->_result, JSON_UNESCAPED_UNICODE);
		$this->_response($result)
	}
}


?>