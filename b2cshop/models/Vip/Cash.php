<?php 
namespace Vip;

class CashModel extends \Mapper\Abstracts
{
    
    protected $_name = 'vip_cash';
    
    protected $_primaryKey ='cash_id';
    
    public function getTableName()
    {
    	return $this->_name;
    }
    
}


?>