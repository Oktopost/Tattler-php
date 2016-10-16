<?php
namespace Tattler\Base\Decorators;


use Tattler\Decorators\DB\RedisDecorator;
use Tattler\Decorators\Network\CurlDecorator;
use Tattler\Decorators\Network\GuzzleDecorator;
use Tattler\Decorators\Network\HttpfulDecorator;


$networkDecoratorCallback = function() {
    if (class_exists('GuzzleHttp\Client')) {
        return new GuzzleDecorator();
    } else if (class_exists('Httpful\Request')) {
        return new HttpfulDecorator();
    } else if (function_exists('curl_init')) {
        return new CurlDecorator();
    } else {
        return false;
    }
};

$dbDecoratorCallback = function()
{
    if (class_exists('Predis\Client'))
    {
        return new RedisDecorator();
    }
    else
    {
        return false;
    }
};


$this->set(INetworkDecorator::class, $networkDecoratorCallback);
$this->set(IDBDecorator::class, $dbDecoratorCallback);