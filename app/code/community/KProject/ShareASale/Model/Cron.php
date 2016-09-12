<?php

/**
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see the license tag below.
 *
 * @author    KProject <support@kproject.pro>
 * @license   http://www.gnu.org/licenses/ GNU General Public License, version 3
 * @copyright 2016 KProject.pro
 */
class KProject_ShareASale_Model_Cron
{
    /**
     * Attempt to send previously failed transactions
     * that are in status pending or specific error_code.
     * Also keeps track of previous attempts. On 5+ attempts
     * it removes the entry and continues to the next one.
     */
    public function submitFailedTransactions()
    {
        /** @var KProject_ShareASale_Model_Orders $item */
        foreach ($this->getFailedOrders() as $item) {
            if ($item->getOrderNumber()) {
                $item->incrementRetryCount()->save();
                /** @var Mage_Sales_Model_Order $mageOrder */
                $mageOrder = Mage::getModel('sales/order')->loadByIncrementId($item->getOrderNumber());

                if (!Mage::helper('kproject_sas')->newTransactionViaApiEnabled($mageOrder->getStoreId())
                    || $item->removeOnTooManyRetries()
                ) {
                    continue;
                }

                Mage::getSingleton('kproject_sas/session')->setParameters($item->getParameters());
                $response = Mage::helper('kproject_sas/transaction')->create($mageOrder);
                Mage::helper('kproject_sas/status')->setKOrderStatus(
                    $mageOrder,
                    KProject_ShareASale_Helper_Status::STATUS_SUCCESS,
                    $response
                );
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
            ->join(array('mage_order' => 'sales_flat_order'), 'mage_order.increment_id = order_number')
            ->where('mage_order.store_id', array('in' => $storeIds));

        return $collection;
    }
}
