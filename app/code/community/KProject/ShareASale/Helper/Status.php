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
     * Figure out if the response is an error or not
     *
     * @param Zend_Http_Response $response
     *
     * @return int
     */
    public function getStatus(Zend_Http_Response $response)
    {
        if ($response->isSuccessful() && strpos($response->getBody(), 'Error Code ') === false) {
            return self::STATUS_SUCCESS;
        }

        return self::STATUS_SAS_ERROR;
    }

    /**
     * Parses out the error code from the
     * response
     *
     * todo-konstantin: does not belong in this helper
     * @param string $body
     *
     * @return mixed
     */
    public function parseErrorCode($body)
    {
        $matches = preg_match('(?<=Code )(.*)(?=\\r)', $body);

        if (!empty($matches[0])) {
            return $matches[0];
        }

        return $body;
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
}
