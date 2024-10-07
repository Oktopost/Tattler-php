<?php
namespace Tattler\Connectors\Network;


use Tattler\Base\Connectors\INetworkConnector;
use Tattler\Exceptions\TattlerNetworkException;


class CurlConnector implements INetworkConnector
{
    private function getCurl(string $endpoint, string $payload, int $timeout = 10)
    {
        $ch = curl_init($endpoint);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
		curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Content-Length: ' . strlen($payload)]
        );

        $query = curl_exec($ch);

		curl_close($ch);

        return $query;
    }

    public function sendPayload(array $tattlerBag): bool
    {
        $query = $this->getCurl($tattlerBag['tattlerUri'], json_encode($tattlerBag['payload']), $tattlerBag['timeout']);

        try
        {
            return (json_decode($query)->status ?? 0) == 200;
        }
        catch (\Throwable $e)
        {
            throw new TattlerNetworkException($query);
        }
    }

    public function syncChannels(array $tattlerBag): ?array
    {
        $query = $this->getCurl($tattlerBag['tattlerUri'], json_encode($tattlerBag['payload']));

        try
        {
            return (json_decode($query)->rooms ?? []);
        }
        catch (\Throwable $e)
        {
			throw new TattlerNetworkException($query);
        }
    }
}