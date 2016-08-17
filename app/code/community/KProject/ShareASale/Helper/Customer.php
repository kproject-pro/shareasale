<?php

/**
 * @author Konstantin Kiritsenko <konstantin@kiritsenko.com>
 */
class KProject_ShareASale_Helper_Customer extends Mage_Core_Helper_Abstract
{

    /**
     * Retrieves an answer
     *
     * @param string | int $customerId
     *
     * @return int | string
     */
    public function getIsNewParam($customerId = null)
    {
        if (!$customerId) {
            return '';
        }

        return $this->isNew($customerId);
    }

    /**
     * @param int | string $customerId
     *
     * @return bool
     */
    public function isNew($customerId)
    {
        $orders = Mage::getResourceModel('sales/order_collection')
                      ->addFieldToFilter('customer_id', $customerId);

        return $orders->getSize() > 1;
    }
}
