<?php

/**
 * @author Konstantin Kiritsenko <konstantin@kiritsenko.com>
 */
class KProject_ShareASale_Model_Cron
{
    /**
     * Attempt to send previously failed transactions
     * that are in status pending or specific error_code.
     */
    public function submitFailedTransactions()
    {
        /** @var KProject_ShareASale_Model_Orders $item */
        foreach ($this->getFailedOrders() as $item) {
            if ($item->getOrderNumber()) {
                /** @var Mage_Sales_Model_Order $mageOrder */
                $mageOrder = Mage::getModel('sales/order')->load($item->getOrderNumber());
                Mage::helper('kproject_sas/transaction')->create($mageOrder);
            }
        }

        return $this;
    }

    /**
     * Removes all the old successful transactions
     */
    public function deleteSuccessfulTransactions()
    {
        /** @var KProject_ShareASale_Model_Mysql4_Orders_Collection $collection */
        $collection = Mage::getModel('kproject_sas/orders')
                          ->getCollection()
                          ->addFieldToFilter(
                              'api_status',
                              array(
                                  'nin' => array(
                                      KProject_ShareASale_Helper_Status::STATUS_SAS_ERROR,
                                      KProject_ShareASale_Helper_Status::STATUS_MAGE_ERROR
                                  )
                              )
                          );

        foreach ($collection as $order) {
            $order->delete();
        }
    }

    /**
     * Get enabled store Id's
     *
     * @return array
     */
    private function getStoreIds()
    {
        $storeIds = array();
        $helper   = Mage::helper('kproject_sas');

        /** @var Mage_Core_Model_Website $website */
        foreach (Mage::app()->getWebsites() as $website) {
            /** @var Mage_Core_Model_Store_Group $group */
            foreach ($website->getGroups() as $group) {
                $stores = $group->getStores();
                /** @var Mage_Core_Model_Store $store */
                foreach ($stores as $store) {
                    if (!$helper->isEnabled($store->getId())
                        || !$helper->newTransactionViaApiEnabled($store->getId())
                    ) {
                        continue;
                    }
                    $storeIds[] = $store->getId();
                }
            }
        }

        return $storeIds;
    }

    /**
     * @return KProject_ShareASale_Model_Mysql4_Orders_Collection
     */
    private function getFailedOrders()
    {
        $storeIds   = $this->getStoreIds();
        $collection = Mage::getModel('kproject_sas/orders')
                          ->getCollection()
                          ->addFieldToFilter(
                              'api_status',
                              array(
                                  'in' => array(
                                      KProject_ShareASale_Helper_Status::STATUS_SAS_ERROR,
                                      KProject_ShareASale_Helper_Status::STATUS_MAGE_ERROR
                                  )
                              )
                          );
        $collection
            ->getSelect()
            ->join(array('mage_order' => 'sales_flat_order'), 'mage_order.order_id = main_table.order_number')
            ->where('mage_order.store_id', array('in' => $storeIds)); //todo: needs to be tested

        return $collection;
    }
}
