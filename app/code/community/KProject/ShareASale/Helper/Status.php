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
     * @return bool
     */
    public function isSuccessful(Zend_Http_Response $response)
    {
        if ($response->isSuccessful() && strpos($response->getBody(), 'Error Code ') === false) {
            return true;
        }

        return false;
    }

    /**
     * Parses out the error code from the
     * response
     *
     * todo-konstantin: does not belong in this helper
     *
     * @param string $body
     *
     * @return mixed
     */
    public function parseErrorCode($body)
    {
        preg_match('/Code\s(.*)\\r/', $body, $matches);

        if (!empty($matches[1])) {
            return $matches[1];
        }

        return $body;
    }

    /**
     * @param Zend_Http_Response $response
     *
     * @return bool | string
     */
    public function getErrorCode($response)
    {
        if ($this->getStatusFromResponse($response) === self::STATUS_SAS_ERROR) {
            return $this->parseErrorCode($response->getBody());
        }

        return false;
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

    /**
     * @param Zend_Http_Response $response
     *
     * @return int
     */
    public function getStatusFromNewResponse($response)
    {
        return $this->getStatusFromResponse($response);
    }

    /**
     * @param Zend_Http_Response $response
     *
     * @return int
     */
    public function getStatusFromVoidResponse($response)
    {
        return $this->getStatusFromResponse($response, self::STATUS_FULL_REFUND);
    }

    /**
     * @param Zend_Http_Response $response
     *
     * @return int
     */
    public function getStatusFromEditResponse($response)
    {
        return $this->getStatusFromResponse($response, self::STATUS_PARTIAL_REFUND);
    }

    /**
     * @param Zend_Http_Response $response
     *
     * @return int
     */
    private function getStatusFromResponse($response, $success = self::STATUS_SUCCESS)
    {
        return $this->isSuccessful($response)
            ? $success
            : self::STATUS_SAS_ERROR;
    }
}
