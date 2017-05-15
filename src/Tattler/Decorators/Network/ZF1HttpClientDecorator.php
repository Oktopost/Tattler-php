<?php
namespace Tattler\Decorators\Network;


use \Zend_Http_Client;
use Tattler\Base\Decorators\INetworkDecorator;


class ZF1HttpClientDecorator implements INetworkDecorator
{
	/** @var Zend_Http_Client */
	private $client;
	
	
	private function getHeaders()
	{
		return [
			'Content-Type: application/json'
		];
	}
	
	
	public function __construct()
	{
		$this->client = new Zend_Http_Client();
	}
	
	
	/**
	 * @param array $tattlerBag
	 * @return bool
	 */
	public function sendPayload(array $tattlerBag)
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
		
		
		if ($this->client->getLastResponse()->getStatus() == 200)
		{
			$body = json_decode($this->client->getLastResponse()->getBody(), true);
			
			if (isset($body['status']) && $body['status'] == 200)
			{
				return true;
			}
		}
		
		return false;
	}
	
	/**
	 * @param array $tattlerBag
	 * @return array|bool
	 */
	public function syncChannels(array $tattlerBag)
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
		
		if ($this->client->getLastResponse()->getStatus() == 200)
		{
			$body = json_decode($this->client->getLastResponse()->getBody(), true);
			
			return isset($body['rooms']) ? $body['rooms'] : false;
		}
		
		return false;
	}
}