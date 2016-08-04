<?php

class KProject_ShareASale_Model_Mysql4_Orders_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{

    public function _construct()
    {
        $this->_init('kproject_sas/orders');
    }
}
