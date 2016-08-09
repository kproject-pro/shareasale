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
            $status   = $this->statusHelper()->isSuccessful($response)
                ? KProject_ShareASale_Helper_Status::STATUS_SUCCESS
                : KProject_ShareASale_Helper_Status::STATUS_SAS_ERROR;

        } catch (Exception $e) {
            $status = $this->statusHelper()->getMageErrorCode();
            Mage::throwException('Error when calling ShareASale New Transaction API: ' . $e->getMessage());
        }


        $time   = Mage::getModel('core/date')->timestamp(time());
        $kOrder = Mage::getModel('kproject_sas/orders')
                      ->setCallDate($time)
                      ->setOrderNumber($magentoOrder->getIncrementId())
                      ->setApiStatus($status);

        if (isset($response) && $status === KProject_ShareASale_Helper_Status::STATUS_SAS_ERROR) {
            $error = $this->statusHelper()->parseErrorCode($response->getBody());
            $kOrder->setErrorCode($error);
        }

        $kOrder->save();

        return $magentoOrder;
    }

    /**
     * Fully void a transaction using ShareASale API
     *
     * @param Mage_Sales_Model_Order $magentoOrder
     *
     * @return bool|Mage_Sales_Model_Order
     */
    public function void(Mage_Sales_Model_Order $magentoOrder)
    {
        /** @var KProject_ShareASale_Model_Orders $kOrder */
        $kOrder = Mage::getModel('kproject_sas/orders')->load($magentoOrder->getIncrementId(), 'order_number');

        if (!$this->isActive($magentoOrder->getStoreId()) || !$kOrder) {
            return false;
        }

        $parameters  = Mage::getSingleton('core/session')->getData('kproject_sas_parameters');
        $credentials = $this->getCredentials($magentoOrder->getStoreId());
        $api         = new KProject_ShareASale_Api($credentials);

        try {
            $response = $api->voidTransaction($kOrder, $parameters);
            $status   = $this->statusHelper()->isSuccessful($response)
                ? KProject_ShareASale_Helper_Status::STATUS_SUCCESS
                : KProject_ShareASale_Helper_Status::STATUS_SAS_ERROR;

        } catch (Exception $e) {
            $status = $this->statusHelper()->getMageErrorCode();
            Mage::throwException('Error when calling ShareASale Void Transaction API: ' . $e->getMessage());
        }
        $kOrder->setApiStatus($status);

        if (isset($response) && $status === KProject_ShareASale_Helper_Status::STATUS_SAS_ERROR) {
            $error = $this->statusHelper()->parseErrorCode($response->getBody());
            $kOrder->setErrorCode($error);
        }

        $kOrder->save();

        return $magentoOrder;
    }

    /**
     * Fully void a transaction using ShareASale API
     *
     * @param Mage_Sales_Model_Order $magentoOrder
     *
     * @return bool|Mage_Sales_Model_Order
     */
    public function edit(Mage_Sales_Model_Order $magentoOrder)
    {
        /** @var KProject_ShareASale_Model_Orders $kOrder */
        $kOrder = Mage::getModel('kproject_sas/orders')->load($magentoOrder->getIncrementId(), 'order_number');

        if (!$this->isActive($magentoOrder->getStoreId()) || !$kOrder) {
            return false;
        }

        $parameters  = Mage::getSingleton('core/session')->getData('kproject_sas_parameters');
        $credentials = $this->getCredentials($magentoOrder->getStoreId());
        $api         = new KProject_ShareASale_Api($credentials);

        try {
            $response = $api->editTransaction($kOrder, $magentoOrder, $parameters);
            $status   = $this->statusHelper()->isSuccessful($response)
                ? KProject_ShareASale_Helper_Status::STATUS_SUCCESS
                : KProject_ShareASale_Helper_Status::STATUS_SAS_ERROR;

        } catch (Exception $e) {
            $status = $this->statusHelper()->getMageErrorCode();
            Mage::throwException('Error when calling ShareASale Edit Transaction API: ' . $e->getMessage());
        }
        $kOrder->setApiStatus($status);

        if (isset($response) && $status === KProject_ShareASale_Helper_Status::STATUS_SAS_ERROR) {
            $error = $this->statusHelper()->parseErrorCode($response->getBody());
            $kOrder->setErrorCode($error);
        }

        $kOrder->save();

        return $magentoOrder;
    }
}
