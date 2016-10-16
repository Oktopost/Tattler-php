<?php
namespace Tattler\Decorators\Network;


use Tattler\Base\Decorators\INetworkDecorator;


/**
 * Class CurlDecorator
 */
class CurlDecorator implements INetworkDecorator
{
    /**
     * @param string $endpoint
     * @param string $payload
     * @return mixed
     */
    private function getCurl($endpoint, $payload)
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

    /**
     * @param array $tattlerBag
     * @return bool
     */
    public function sendPayload(array $tattlerBag)
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

    /**
     * @param array $tattlerBag
     * @return array|bool
     */
    public function syncChannels(array $tattlerBag)
    {
        $query = $this->getCurl($tattlerBag['tattlerUri'], json_encode($tattlerBag['payload']));

        try
        {
            return json_decode($query)->rooms;
        }
        catch (\Exception $e)
        {
            return false;
        }
    }
}