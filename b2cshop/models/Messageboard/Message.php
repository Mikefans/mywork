<?php 
namespace Messageboard;

class MessageModel extends \Mapper\Abstracts
{
    
    protected $_name = 'messageboard_message';
    
    protected $_primaryKey ='message_id';
    
    public function getTableName()
    {
    	return $this->_name;
    }

    public function getLists($params)
    {
        $user = \Yaf\Session::getInstance()->m_user;
        $pageNum = isset($params['page_num']) ? (int)$params['page_num'] : 1;
        $pageSize = isset($params['page_size']) ? (int)$params['page_size'] : 5;
        $result = $this->where(array(
        	'user_id' => $user['user_id'],
            'vip_delete' => 0
        ))
                ->orderBy('create_time','desc')
                ->skip(($pageNum-1) * $pageSize)
                ->limit($pageSize)
                ->get();
        $totalRecords = $this->count();
        return array(
            'data' => $result,
            'page_count' => ceil($totalRecords / $pageSize),
            'page_num' => $pageNum
        );
    }
      
    public function save($params)
    {
    	$content = $params['content'];
    	$session = \Yaf\Session::getInstance();
    	$user = $session->m_user;
    	$data = array(
    		'message_content' => $content,
    	    'user_id' => $user['user_id']
    	);
    	$id = $this->insert($data);
    	return array('id' => $id);
    }
  
    public function deletes($params)
    {
    	$id = $params['message_id'];
    	$this->where(array(
    		'message_id' => $id
    	))
    	   ->update(array('vip_delete' =>1));
    	return array('id' => $id);
    }
}


?>