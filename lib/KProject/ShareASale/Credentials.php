<?php

/**
 * @author Konstantin Kiritsenko <konstantin@kiritsenko.com>
 */
class KProject_ShareASale_Credentials
{
    /** @var string $token */
    protected $token;
    /** @var string $merchantId */
    protected $merchantId;
    /** @var string $secretKey */
    protected $secretKey;

    /**
     * @param string $token
     *
     * @return KProject_ShareASale_Credentials
     */
    public function setToken($token)
    {
        $this->token = $token;

        return $this;
    }

    /**
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @param string $merchantId
     *
     * @return KProject_ShareASale_Credentials
     */
    public function setMerchantId($merchantId)
    {
        $this->merchantId = $merchantId;

        return $this;
    }

    /**
     * @return string
     */
    public function getMerchantId()
    {
        return $this->merchantId;
    }

    /**
     * @param string $secretKey
     *
     * @return KProject_ShareASale_Credentials
     */
    public function setSecretKey($secretKey)
    {
        $this->secretKey = $secretKey;

        return $this;
    }

    /**
     * @return string
     */
    public function getSecretKey()
    {
        return $this->secretKey;
    }
}
