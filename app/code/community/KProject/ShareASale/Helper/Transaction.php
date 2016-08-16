<?php

/**
 * @author Konstantin Kiritsenko <konstantin@kiritsenko.com>
 */
class KProject_ShareASale_Helper_Transaction extends KProject_ShareASale_Helper_Data
{

    /**
     * Create new transaction using the ShareASale API
     *
     * @param Mage_Sales_Model_Order $order
     *
     * @return bool | Mage_Sales_Model_Order
     */
    public function create(Mage_Sales_Model_Order $order)
    {
        $parameters = Mage::getSingleton('core/session')->getData('kproject_sas_parameters');
        if (!$this->isActive($order->getStoreId()) || !$parameters) {
            return false;
        }

        $credentials = $this->getCredentials($order->getStoreId());
        $api         = new KProject_ShareASale_Api($credentials);

        try {
            $response = $api->createNewTransaction($order, $parameters);
            $status   = $this->statusHelper()->getStatusFromNewResponse($response);
        } catch (Exception $e) {
            $status = $this->statusHelper()->getMageErrorCode();
            Mage::getSingleton('core/session')->addError(
                'Error when calling ShareASale New Transaction API: ' . $e->getMessage()
            );
        }

        $kOrder = Mage::getModel('kproject_sas/orders')
                      ->setCallDate($order->getCreatedAtDate())
                      ->setOrderNumber($order->getIncrementId())
                      ->setApiStatus($status);

        if (!empty($response) && !$this->statusHelper()->isSuccessful($response)) {
            $error = $this->statusHelper()->getErrorCode($response);
            $this->statusHelper()->logError($status, $response);
            if ($error) {
                $kOrder->setErrorCode($error);
            }
        }

        $kOrder->save();

        return $order;
    }

    /**
     * Fully void a transaction using ShareASale API
     *
     * @param Mage_Sales_Model_Order $order
     *
     * @return bool | Mage_Sales_Model_Order
     */
    public function void(Mage_Sales_Model_Order $order)
    {
        if (!$this->isActive($order->getStoreId())) {
            return false;
        }

        $credentials = $this->getCredentials($order->getStoreId());
        $api         = new KProject_ShareASale_Api($credentials);
        $parameters  = Mage::getSingleton('core/session')->getData('kproject_sas_parameters');
        $parameters  = $parameters ? $parameters : array();

        try {
            $response = $api->voidTransaction($order, $parameters);
            $status   = $this->statusHelper()->getStatusFromVoidResponse($response);
        } catch (Exception $e) {
            $status = $this->statusHelper()->getMageErrorCode();
            Mage::getSingleton('core/session')->addError(
                'Error when calling ShareASale Void Transaction API: ' . $e->getMessage()
            );
        }

        if (!empty($response)) {
            $error = $this->statusHelper()->getErrorCode($response);
            $this->statusHelper()->logError($status, $response);
            $this->setOrderStatus($order, $status, $error);
        }


        return $order;
    }

    /**
     * Make an edit to a transaction using ShareASale API
     *
     * @param Mage_Sales_Model_Order $order
     *
     * @return bool | Mage_Sales_Model_Order
     */
    public function edit(Mage_Sales_Model_Order $order)
    {
        if (!$this->isActive($order->getStoreId())) {
            return false;
        }

        $credentials = $this->getCredentials($order->getStoreId());
        $api         = new KProject_ShareASale_Api($credentials);
        $parameters  = Mage::getSingleton('core/session')->getData('kproject_sas_parameters');
        $parameters  = $parameters ? $parameters : array();

        try {
            $response = $api->editTransaction($order, $parameters);
            $status   = $this->statusHelper()->getStatusFromEditResponse($response);
        } catch (Exception $e) {
            $status = $this->statusHelper()->getMageErrorCode();
            Mage::getSingleton('core/session')->addError(
                'Error when calling ShareASale Edit Transaction API: ' . $e->getMessage()
            );
        }

        if (!empty($response)) {
            $error = $this->statusHelper()->getErrorCode($response);
            $this->statusHelper()->logError($status, $response);
            $this->setOrderStatus($order, $status, $error);
        }

        return $order;
    }

    /**
     * Helps set status for edit & void calls as
     * they could be running without New Transaction
     * API enabled
     *
     * @param Mage_Sales_Model_Order $order
     * @param int                    $status
     * @param bool                   $error
     *
     * @return bool
     */
    public function setOrderStatus($order, $status, $error = false)
    {
        if (!Mage::helper('kproject_sas')->newTransactionViaApiEnabled($order->getStoreId())) {
            return false;
        }

        /** @var KProject_ShareASale_Model_Orders $kOrder */
        $kOrder = Mage::getModel('kproject_sas/orders')->load($order->getIncrementId(), 'order_number');
        if (!$kOrder->getId()) {
            return false;
        }

        if ($error) {
            $kOrder->setErrorCode($error);
        }

        $kOrder->setApiStatus($status);
        $kOrder->save();

        return true;
    }

}
