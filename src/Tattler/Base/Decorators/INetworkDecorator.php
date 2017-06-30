<?php

namespace Tattler\Base\Decorators;


/**
 * @skeleton
 */
interface INetworkDecorator
{
    public function sendPayload(array $tattlerBag): bool;
    public function syncChannels(array $tattlerBag): ?array;
}