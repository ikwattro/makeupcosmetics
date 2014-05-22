<?php

namespace Store\PaymentBundle\StatusResolver;

class OgoneStatusResolver
{
    protected $statusCodes = array(
        0 => 'INVALID',
        1 => 'CANCELLED BY CUSTOMER',
        2 => 'DECLINED',
        4 => 'STORED',
        41 => 'WAITING FOR PAYMENT',
        5 => 'AUTHORIZED',
        6 => 'AUTHORIZED AND CANCELLED',
        7 => 'DELETED'
    );

    private $code;

    public function __construct($code)
    {
        $this->code = $code;

        return $this;
    }

    public function getExplanation()
    {
        return $this->statusCodes[$this->code];
    }

    public function isPaymentValid()
    {
        if ($this->getExplanation($this->code) == 'AUTHORIZED') {
            return true;
        }
        return false;
    }

    public function getStatusCode()
    {
        return $this->code;
    }
}