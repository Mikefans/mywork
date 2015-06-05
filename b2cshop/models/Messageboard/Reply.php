<?php 
namespace Messageboard;

class ReplyModel extends \Mapper\Abstracts
{
    
    protected $_name = 'messageboard_reply';
    
    protected $_primaryKey ='reply_id';
    
    public function getTableName()
    {
    	return $this->_name;
    }

    public function getLists($params)
    {
        $pageNum = isset($params['page_num']) ? (int)$params['page_num'] : 1;
        $pageSize = isset($params['page_size']) ? (int)$params['page_size'] : 5;
        $result = $this->where(array(
            'message_id' => $params['message_id'],
        ))
            ->orderBy('create_time','desc')
            ->skip(($pageNum-1) * $pageSize)
            ->limit($pageSize)
            ->get();
        $totalRecords = $this->count();
        
        $messageModel = \Messageboard\MessageModel::getInstance();
        $message = $messageModel->where(array(
            'message_id' => $params['message_id'],
        ))
                ->first();
        return array(
            'message' => $message,
            'data' => $result,
            'page_count' => ceil($totalRecords / $pageSize),
            'page_num' => $pageNum
        );
    }
    
    public function save($params)
    {
    	$data = array(
    		'reply_content' => $params['content'],
    	    'message_id' => $params['message_id'],
    	    'is_self' => 1
    	);
    	$this->insert($data);
    }
}


?>