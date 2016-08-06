<?php

/**
 * @author Konstantin Kiritsenko <konstantin@kiritsenko.com>
 */
class KProject_ShareASale_Helper_Transaction extends KProject_ShareASale_Helper_Data
{
    /**
     * Create new transaction using the ShareASale API
     *
     * @param Mage_Sales_Model_Order $magentoOrder
     *
     * @return bool|Mage_Sales_Model_Order
     */
    public function create(Mage_Sales_Model_Order $magentoOrder)
    {
        if (!$this->isActive($magentoOrder->getStoreId())) {
            return false;
        }

        $parameters  = Mage::getSingleton('core/session')->getData('kproject_sas_parameters');
        $credentials = $this->getCredentials($magentoOrder->getStoreId());
        $api         = new KProject_ShareASale_Api($credentials);

        try {
            $response = $api->createNewTransaction($magentoOrder, $parameters);
            $status   = $this->statusHelper()->getStatus($response);
        } catch (Exception $e) {
            $status = $this->statusHelper()->getMageErrorCode();
            Mage::throwException('Error when calling ShareASale API: ' . $e->getMessage());
        }

        $time   = Mage::getModel('core/date')->timestamp(time());
        $kOrder = Mage::getModel('kproject_sas/orders')
                      ->setCallDate($time)
                      ->setOrderNumber($magentoOrder->getIncrementId())
                      ->setApiStatus($status);

        if (isset($response) && $this->statusHelper()->isError($status)) {
            $error = $this->statusHelper()->parseErrorCode($response->getBody());
            $kOrder->setErrorCode($error);
        }

        $kOrder->save();

        return $magentoOrder;
    }

    public function void(Mage_Sales_Model_Order $magentoOrder)
    {
        if (!$this->isActive($magentoOrder->getStoreId())) {
            return false;
        }

        return $magentoOrder;
    }
}
