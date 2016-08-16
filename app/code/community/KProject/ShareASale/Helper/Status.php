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
     * @var array - error text that can come back from the API
     */
    private $errorMap = array(
        'Error Code',
        'Transaction Not Found'
    );

    /**
     * Figure out if the response is an error or not
     *
     * @param Zend_Http_Response $response
     *
     * @return bool
     */
    public function isSuccessful(Zend_Http_Response $response)
    {
        if ($response->isSuccessful() && !$this->strposArray($response->getBody(), $this->errorMap)) {
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
    private function parseErrorCode($body)
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
        $code = $this->parseErrorCode($response->getBody());
        if (is_integer($code)) {
            return $code;
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
        return $this->isSuccessful($response)
            ? self::STATUS_SUCCESS
            : self::STATUS_SAS_ERROR;
    }

    /**
     * @param Zend_Http_Response $response
     *
     * @return int
     */
    public function getStatusFromVoidResponse($response)
    {
        return $this->isSuccessful($response)
            ? self::STATUS_FULL_REFUND
            : self::STATUS_SAS_ERROR;
    }

    /**
     * @param Zend_Http_Response $response
     *
     * @return int
     */
    public function getStatusFromEditResponse($response)
    {
        return $this->isSuccessful($response)
            ? self::STATUS_PARTIAL_REFUND
            : self::STATUS_SAS_ERROR;
    }

    /**
     * Helps log API errors to mage var/log/system.log
     *
     * @param int    $status
     * @param string $response
     */
    public function logError($status, $response)
    {
        if ($this->isError($status)) {
            Mage::log('KProject ShareASale error from API: ' . $response);
        }
    }

    /**
     * @param string         $haystack
     * @param array | string $needles
     *
     * @return int|false
     */
    private function strposArray($haystack, $needles)
    {
        if (is_array($needles)) {
            foreach ($needles as $str) {
                if (is_array($str)) {
                    $pos = $this->strposArray($haystack, $str);
                } else {
                    $pos = strpos($haystack, $str);
                }
                if ($pos !== false) {
                    return $pos;
                }
            }
        } else {
            return strpos($haystack, $needles);
        }

        return false;
    }
}
