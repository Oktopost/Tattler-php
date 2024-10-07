<?php
namespace Tattler\Connectors\Network;


use Gazelle\Gazelle;
use Gazelle\Request;

use Tattler\Base\Connectors\INetworkConnector;
use Tattler\Exceptions\TattlerNetworkException;


class GazelleConnector implements INetworkConnector
{
	private function request(string $url, array $payload, int $timeout = 10): Request
	{
		/** @var Request $result */
		$result = (new Gazelle())
			->request($url, ['Content-Type' => 'application/json'])
			->setBody(jsonencode($payload))
			->setExecutionTimeout($timeout)
			->setMethod('POST');
		
		return $result;
	}

	public function sendPayload(array $data): bool
	{
		try
		{
			$this->request($data['tattlerUri'], $data['payload'], $data['timeout'])
				->send();
			
			return true;
		}
		catch (\Throwable $e)
		{
			throw new TattlerNetworkException($e->getMessage(), $e->getCode(), $e);
		}
	}

	public function syncChannels(array $data): ?array
	{
		try
		{
			$response = $this->request($data['tattlerUri'], $data['payload'])
				->queryJSON();
			
			return $response['rooms'] ?? [];
		}
		catch (\Throwable $e)
		{
			throw new TattlerNetworkException($e->getMessage(), $e->getCode(), $e);
		}
	}
}