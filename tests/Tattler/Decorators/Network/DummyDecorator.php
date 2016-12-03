<?php
namespace Tests\Tattler\Decorators\Network;


use Tattler\Base\Decorators\INetworkDecorator;


/**
 * Class DummyDecorator
 */
class DummyDecorator implements INetworkDecorator
{
	/**
	 * @param array $tattlerBag
	 * @return bool
	 */
	public function sendPayload(array $tattlerBag)
	{
		return true;
	}
	
	/**
	 * @param array $tattlerBag
	 * @return array|bool
	 */
	public function syncChannels(array $tattlerBag)
	{
		return explode(',', $tattlerBag['payload']['rooms']);
	}
}