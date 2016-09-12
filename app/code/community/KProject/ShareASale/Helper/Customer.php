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
class KProject_ShareASale_Helper_Customer extends Mage_Core_Helper_Abstract
{

    /**
     * Retrieves an answer if the customer
     * is new or not. If guest will return an
     * empty string.
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

        return $this->isNew($customerId) ? 1 : 0;
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

        return $orders->getSize() <= 1;
    }
}
