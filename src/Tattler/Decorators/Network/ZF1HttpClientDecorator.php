<?php
namespace Tattler\Decorators\Network;


use Tattler\Base\Decorators\INetworkDecorator;
use Tattler\Exceptions\TattlerNetworkException;

use Zend_Http_Client;


class ZF1HttpClientDecorator implements INetworkDecorator
{
	/** @var Zend_Http_Client */
	private $client;
	
	public function __construct()
	{
		$this->client = new Zend_Http_Client();
	}
	
	public function sendPayload(array $tattlerBag): bool
	{
		$this->client
			->setUri($tattlerBag['tattlerUri'])
			->setMethod(\Zend_Http_Client::POST)
			->setHeaders($this->getHeaders())
			->setRawData(json_encode($tattlerBag['payload']));
		
		try 
		{
			$this->client->request();
		} 
		catch (\Exception $e) 
		{
			throw new TattlerNetworkException('Failed to send payload');
		}
		
		$body = $this->client->getLastResponse()->getBody();
		
		if ($this->client->getLastResponse()->getStatus() == 200) {
			$body = json_decode($body, true);
			
			if (isset($body['status']) && $body['status'] == 200) {
				return true;
			}
			else
			{
				throw new TattlerNetworkException($body);
			}
		}
		
		throw new TattlerNetworkException($body);
	}
	
	private function getHeaders()
	{
		return [
			'Content-Type: application/json'
		];
	}
	
	public function syncChannels(array $tattlerBag): ?array
	{
		$this->client
			->setUri($tattlerBag['tattlerUri'])
			->setMethod(\Zend_Http_Client::POST)
			->setHeaders($this->getHeaders())
			->setRawData(json_encode($tattlerBag['payload']));
		
		try 
		{
			$this->client->request();
		} 
		catch (\Exception $e) 
		{
			throw new TattlerNetworkException('Failed to sync channels');
		}
		
		if ($this->client->getLastResponse()->getStatus() != 200) 
		{
			throw new TattlerNetworkException($this->client->getLastResponse()->getBody());
		}
		
		$body = json_decode($this->client->getLastResponse()->getBody(), true);
		
		return isset($body['rooms']) ? $body['rooms'] : false;
	}
}