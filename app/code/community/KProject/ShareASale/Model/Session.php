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
class KProject_ShareASale_Model_Session extends Mage_Core_Model_Session_Abstract
{
    const KEY = 'kproject_parameters';

    public function __construct()
    {
        $this->init('kproject_sas');
    }

    /**
     * @return array
     */
    public function getParameters()
    {
        $params = $this->getData(self::KEY);

        if (empty($params)) {
            $params = array();
        }

        return $params;
    }

    /**
     * @param array $parameters
     *
     * @return Varien_Object
     */
    public function setParameters($parameters)
    {
        return $this->setData(self::KEY, $parameters);
    }

    /**
     * @return Varien_Object
     */
    public function unsetParameters()
    {
        return $this->unsetData(self::KEY);
    }
}
