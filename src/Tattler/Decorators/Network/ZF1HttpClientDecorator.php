<?php
namespace Tattler\Decorators\Network;


use Tattler\Base\Decorators\INetworkDecorator;
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
			return false;
		}
		
		
		if ($this->client->getLastResponse()->getStatus() == 200) {
			$body = json_decode($this->client->getLastResponse()->getBody(), true);
			
			if (isset($body['status']) && $body['status'] == 200) {
				return true;
			}
		}
		
		return false;
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
			return null;
		}
		
		if ($this->client->getLastResponse()->getStatus() != 200) {
			return null;
		}
		
		$body = json_decode($this->client->getLastResponse()->getBody(), true);
		
		return isset($body['rooms']) ? $body['rooms'] : false;
	}
}