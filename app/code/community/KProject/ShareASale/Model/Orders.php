<?php

/**
 * @author Konstantin Kiritsenko <konstantin@kiritsenko.com>
 *
 * @method string getId()
 * @method KProject_ShareASale_Model_Orders setId($int_id)
 * @method string getOrderNumber()
 * @method KProject_ShareASale_Model_Orders setOrderNumber($str_order_number)
 * @method int getApiStatus()
 * @method KProject_ShareASale_Model_Orders setApiStatus($int_status)
 * @method string getErrorCode()
 * @method int getRetryCount()
 * @method KProject_ShareASale_Model_Orders setRetryCount($int_retry_count)
 */
class KProject_ShareASale_Model_Orders extends Mage_Core_Model_Abstract
{
    const MAX_RETRY_COUNT = 5;

    protected function _construct()
    {
        $this->_init('kproject_sas/orders');
    }

    /**
     * Helps set the parameters with serializing
     * if needed
     *
     * @param array | string $parameters
     *
     * @return $this
     */
    public function setParameters($parameters)
    {
        if (empty($parameters)) {
            return $this;
        } elseif (is_array($parameters)) {
            $parameters = Zend_Serializer::serialize($parameters);
        }
        $this->setData('parameters', $parameters);

        return $this;
    }

    /**
     * If parameters are set, then it returns
     * an un-serialized array
     *
     * @return array
     * @throws Zend_Serializer_Exception
     */
    public function getParameters()
    {
        $parameters = $this->getData('parameters');

        if (!empty($parameters)) {
            $parameters = Zend_Serializer::unserialize($parameters);
        } else {
            $parameters = array();
        }

        return $parameters;
    }

    /**
     * @param int $errorCode
     *
     * @return $this
     */
    public function setErrorCode($errorCode)
    {
        if ($errorCode) {
            $this->setData('error_code', $errorCode);
        }

        return $this;
    }

    /**
     * Increment retry counter by one
     *
     * @return $this
     */
    public function incrementRetryCount()
    {
        $count = $this->getRetryCount();
        $count++;
        $this->setRetryCount($count);

        return $this;
    }

    /**
     * Removes entry if max retry attempts
     * were reached
     *
     * @return bool
     */
    public function removeOnTooManyRetries()
    {
        if ($this->getRetryCount() >= self::MAX_RETRY_COUNT) {
            $this->delete();

            return true;
        }

        return false;
    }
}
