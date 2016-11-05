<?php

namespace Tattler\Base\Decorators;


/**
 * @skeleton
 */
interface INetworkDecorator
{
    /**
     * @param array $tattlerBag
     * @return bool
     */
    public function sendPayload(array $tattlerBag);

    /**
     * @param array $tattlerBag
     * @return array|bool
     */
    public function syncChannels(array $tattlerBag);
}