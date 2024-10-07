<?php
namespace Tests\Tattler\Connectors\Network;


use Tattler\Base\Connectors\INetworkConnector;


/**
 * Class DummyConnector
 */
class DummyConnector implements INetworkConnector
{
	public function sendPayload(array $tattlerBag): bool
	{
		return true;
	}
	
	public function syncChannels(array $tattlerBag): ?array
	{
		return explode(',', $tattlerBag['payload']['rooms']);
	}
}