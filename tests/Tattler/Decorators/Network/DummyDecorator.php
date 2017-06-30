<?php
namespace Tests\Tattler\Decorators\Network;


use Tattler\Base\Decorators\INetworkDecorator;


/**
 * Class DummyDecorator
 */
class DummyDecorator implements INetworkDecorator
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