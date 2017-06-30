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
	public function sendPayload(array $tattlerBag): bool
	{
		try 
		{
			$result = Request::post($tattlerBag['tattlerUri'])
				->body($tattlerBag['payload'])
				->sendsAndExpectsType(Mime::JSON)
				->send();
		} 
		catch (\Exception $e) 
		{
			return false;
		}
		
		if ($result->hasErrors())
			return false;
		
		return true;
	}
	
	public function syncChannels(array $tattlerBag): ?array
	{
		try {
			$result = Request::post($tattlerBag['tattlerUri'])
				->body($tattlerBag['payload'])
				->sendsAndExpectsType(Mime::JSON)
				->send();
		} 
		catch (\Exception $e) 
		{
			return null;
		}
		
		if ($result->hasErrors())
			return null;
		
		return $result->body->rooms;
	}
}