<?php
namespace Tattler\Decorators\Network;


use Httpful\Mime;
use Httpful\Request;
use Tattler\Base\Decorators\INetworkDecorator;
use Tattler\Exceptions\TattlerNetworkException;


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
			throw new TattlerNetworkException('Failed to send payload');
		}
		
		if ($result->hasErrors())
		{
			throw new TattlerNetworkException($result->raw_body);
		}
		
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
			throw new TattlerNetworkException('Failed to sync channels');
		}
		
		if ($result->hasErrors())
		{
			throw new TattlerNetworkException($result->raw_body);
		}
		
		return $result->body->rooms;
	}
}