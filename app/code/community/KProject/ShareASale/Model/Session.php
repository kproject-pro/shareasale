<?php


/**
 *
 * @author Konstantin Kiritsenko <konstantin@kiritsenko.com>
 */
class KProject_ShareASale_Model_Session extends Mage_Core_Model_Session_Abstract
{
    public function __construct()
    {
        $this->init('kproject_sas');
    }

    /**
     * @return array
     */
    public function getParameters()
    {
        $params = $this->getData('parameters');

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
        return $this->setData('parameters', $parameters);
    }

    /**
     * @return Varien_Object
     */
    public function unsetParameters()
    {
        return $this->unsetData('parameters');
    }
}
