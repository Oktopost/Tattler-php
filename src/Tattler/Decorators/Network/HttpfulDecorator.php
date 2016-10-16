<?php
namespace Tattler\Decorators\Network;


use Httpful\Mime;
use Httpful\Request;
use Tattler\Base\Decorators\INetworkDecorator;


/**
 * Class HttpfulDecorator
 */
class HttpfulDecorator implements INetworkDecorator
{
    /**
     * @param array $tattlerBag
     * @return bool
     */
    public function sendPayload(array $tattlerBag)
    {
        $result = Request::post($tattlerBag['tattlerUri'])
            ->body($tattlerBag['payload'])
            ->sendsAndExpectsType(Mime::JSON)
            ->send();

        if ($result->hasErrors())
            return false;

        return true;
    }

    /**
     * @param array $tattlerBag
     * @return bool
     */
    public function syncChannels(array $tattlerBag)
    {
        $result = Request::post($tattlerBag['tattlerUri'])
            ->body($tattlerBag['payload'])
            ->sendsAndExpectsType(Mime::JSON)
            ->send();

        if ($result->hasErrors())
            return false;

        return $result->body->rooms;
    }
}