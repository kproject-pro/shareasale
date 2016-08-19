<?php

/**
 * @author Konstantin Kiritsenko <konstantin@kiritsenko.com>
 */
class KProject_ShareASale_Helper_Status extends Mage_Core_Helper_Abstract
{
    const STATUS_SUCCESS        = 1;
    const STATUS_PARTIAL_REFUND = 2;
    const STATUS_FULL_REFUND    = 3;
    const STATUS_SAS_ERROR      = 4;
    const STATUS_MAGE_ERROR     = 5;

    /**
     * @var array - error text that can come back from the API
     */
    private $errorMap = array(
        'Error Code',
        'Transaction Not Found'
    );

    /**
     * Figure out if the response is an error or not
     *
     * @param Zend_Http_Response $response
     *
     * @return bool
     */
    public function isSuccessful(Zend_Http_Response $response)
    {
        if ($response->isSuccessful() && !$this->strposArray($response->getBody(), $this->errorMap)) {
            return true;
        }

        return false;
    }

    /**
     * @param Zend_Http_Response $response
     *
     * @return bool | string
     */
    public function getErrorCode($response)
    {
        $code = $this->parseErrorCode($response->getBody());

        if (is_numeric($code)) {
            return $code;
        }

        return false;
    }

    /**
     * Checks if the passed status is an error
     *
     * @param int $status
     *
     * @return bool
     */
    public function isError($status)
    {
        return $status === self::STATUS_SAS_ERROR || $status === self::STATUS_MAGE_ERROR;
    }

    /**
     * Just returns the mage error code
     *
     * @return int
     */
    public function getMageErrorCode()
    {
        return self::STATUS_MAGE_ERROR;
    }

    /**
     * @param Zend_Http_Response $response
     * @param int                $successStatus
     *
     * @return int
     */
    public function getStatusFromResponse($response, $successStatus = self::STATUS_SUCCESS)
    {
        return $this->isSuccessful($response)
            ? $successStatus
            : self::STATUS_SAS_ERROR;
    }

    /**
     * Helps set status for edit & void calls as
     * they could be running without New Transaction
     * API enabled
     *
     * @param Mage_Sales_Model_Order  $order
     * @param int                     $successStatus - 1,2,3
     * @param Zend_Http_Response|null $response
     *
     * @return mixed
     */
    public function setKOrderStatus(Mage_Sales_Model_Order $order, $successStatus, Zend_Http_Response $response = null)
    {
        if (!Mage::helper('kproject_sas')->newTransactionViaApiEnabled($order->getStoreId())) {
            return false;
        }

        $kOrder = Mage::getModel('kproject_sas/orders')->load($order->getIncrementId(), 'order_number');

        if (!$kOrder->getId()) {
            $kOrder->setOrderNumber($order->getIncrementId());
        }

        if (!$response) {
            $status = $this->getMageErrorCode();
        } else {
            $status = $this->getStatusFromResponse($response, $successStatus);
            $error  = $this->getErrorCode($response);
            $kOrder->setErrorCode($error);
        }
        $parameters = Mage::getSingleton('kproject_sas/session')->getParameters();

        try {
            $kOrder->setParameters($parameters)
                   ->setApiStatus($status)
                   ->save();
        } catch (Exception $e) {
            Mage::logException($e);
        }

        return $kOrder;
    }

    /**
     * Parses out the error code from the
     * response
     *
     * @param string $body
     *
     * @return mixed
     */
    private function parseErrorCode($body)
    {
        $body = trim($body);
        preg_match('/Code\s(.*)/', $body, $matches);

        if (!empty($matches[1])) {
            return $matches[1];
        }

        return $body;
    }

    /**
     * @param string         $haystack
     * @param array | string $needles
     *
     * @return int|false
     */
    private function strposArray($haystack, $needles)
    {
        $haystack = trim($haystack);

        if (is_array($needles)) {
            foreach ($needles as $str) {
                if (is_array($str)) {
                    $pos = $this->strposArray($haystack, $str);
                } else {
                    $pos = strpos($haystack, $str);
                }
                if ($pos !== false) {
                    return $pos;
                }
            }
        } else {
            return strpos($haystack, $needles);
        }

        return false;
    }
}
