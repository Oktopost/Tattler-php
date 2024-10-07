<?php

namespace Tattler\Base\Connectors;


/**
 * @skeleton
 */
interface INetworkConnector
{
    public function sendPayload(array $tattlerBag): bool;
    public function syncChannels(array $tattlerBag): ?array;
}