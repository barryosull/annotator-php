<?php

namespace AnnotatorTests\Flysystem;

use Annotator\Flysystem\Gateway;
use Annotator\Flysystem\GatewayFlysystem;

class GatewayFlysystemTest extends GatewayTest
{
    private $gateway;

    protected function gateway(): Gateway
    {
        if (is_null($this->gateway)) {
            $this->gateway = new GatewayFlysystem();
        }
        return $this->gateway;
    }
}