<?php
namespace Tools;
class Hlg extends \Exception{
	protected $_url;
	protected $_code;
	protected $_subCode;
	protected $_bugId;
	protected $_info;
    
    public function setHlgUrl($url)
    {
    	$this->_url = $url;
    	return $this;
    }
    
    public function setHlgCode($code){
    	$this->_code = $code;
        return $this;
    }
    
    public function setHlgSubCode($subCode){
    	$this->_subCode = $subCode;
        return $this;
    }
    
    public function setHlgMsg($msg){
        $this->message = $msg;
        return $this;
    }
    
    public function getHlgUrl()
    {
    	return $this->_url;
    }
    
    public function getHlgCode(){    
    	return $this->_code;	
    }
    
    public function getHlgSubCode(){
    	return $this->_subCode;
    }
    
    public function getHlgMsg(){
    	return $this->message;
    }
}