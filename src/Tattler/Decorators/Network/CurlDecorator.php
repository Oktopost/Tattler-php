<?php
namespace Tattler\Decorators\Network;


use Tattler\Base\Decorators\INetworkDecorator;


/**
 * Class CurlDecorator
 */
class CurlDecorator implements INetworkDecorator
{
    private function getCurl(string $endpoint, string $payload)
    {
        $ch = curl_init($endpoint);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
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
        $query = $this->getCurl($tattlerBag['tattlerUri'], json_encode($tattlerBag['payload']));

        try
        {
            return json_decode($query)->status == 200;
        }
        catch (\Exception $e)
        {
            return false;
        }
    }

    public function syncChannels(array $tattlerBag): ?array
    {
        $query = $this->getCurl($tattlerBag['tattlerUri'], json_encode($tattlerBag['payload']));

        try
        {
            return json_decode($query)->rooms;
        }
        catch (\Exception $e)
        {
            return null;
        }
    }
}