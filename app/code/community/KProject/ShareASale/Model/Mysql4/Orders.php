<?php

/**
 * @author Konstantin Kiritsenko <konstantin@kiritsenko.com>
 */
class KProject_ShareASale_Model_Mysql4_Orders extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init('kproject_sas/orders', 'id');
    }
}
