<?php
class MessageController extends Controllers_Abstract {
    
    
   public function listAction() 
   {
        $params = $this->getParams();
        $messageMapper = \Messageboard\MessageModel::getInstance();
        $result = $messageMapper->getLists($params);      
        $this->getView()->assign('messes',$result['data']);
        $this->getView()->assign('pageCount',$result['page_count']);
        $this->getView()->assign('pageNum',$result['page_num']);
   }
   
   public function saveAction()
   {
       $this->disableView();
       $params = $this->getParams();
       $messageMapper = \Messageboard\MessageModel::getInstance();
       $result = $messageMapper->save($params);      
       $this->response($result);
   }
   
   public function replyAction()
   {
       $params = $this->getParams();
       $replyMapper = \Messageboard\ReplyModel::getInstance();
       $result = $replyMapper->getLists($params);
       $this->getView()->assign('replys',$result['data']);
       $this->getView()->assign('pageCount',$result['page_count']);
       $this->getView()->assign('pageNum',$result['page_num']);
       $this->getView()->assign('mess',$result['message']);
   }
   
   public function delAction()
   {
       $this->disableView();
       $params = $this->getParams();
       $messageMapper = \Messageboard\MessageModel::getInstance();
       $result = $messageMapper->deletes($params);
       $this->response($result);
   }
   
   public function replySaveAction()
   {
       $this->disableView();
       $params = $this->getParams();
      $replyMapper = \Messageboard\ReplyModel::getInstance();
       $result = $replyMapper->save($params);
       $this->response($result);
   }
}
?>

