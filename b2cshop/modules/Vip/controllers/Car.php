<?php
class CarController extends Controllers_Abstract {
    
   public function addToCarAction(){
        $this->disableView();
        $params= $this->getParams();
        $carMapper  = \Vip\CarModel::getInstance();
        $result = $carMapper->addToCar($params);
        $this->response($result);
   }
}
?>

