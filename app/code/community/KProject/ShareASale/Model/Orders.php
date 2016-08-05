<?php

/**
 * @author Konstantin Kiritsenko <konstantin@kiritsenko.com>
 *
 * @method string getId()
 * @method KProject_ShareASale_Model_Orders setId($int_id)
 * @method string getOrderNumber()
 * @method KProject_ShareASale_Model_Orders setOrderNumber($str_order_number)
 * @method string getCallDate()
 * @method KProject_ShareASale_Model_Orders setCallDate($str_date)
 * @method int getApiStatus()
 * @method KProject_ShareASale_Model_Orders setApiStatus($int_status)
 * @method string getErrorCode()
 * @method KProject_ShareASale_Model_Orders setErrorCode($str_code)
 */
class KProject_ShareASale_Model_Orders extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('kproject_sas/orders');
    }
}
