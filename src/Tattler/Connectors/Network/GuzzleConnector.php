<?php
namespace Tattler\Connectors\Network;


use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;

use Tattler\Base\Connectors\INetworkConnector;
use Tattler\Exceptions\TattlerNetworkException;


class GuzzleConnector implements INetworkConnector
{
    /** @var Client $client */
    private $client;


    public function __construct()
    {
        $this->client = new Client(['headers' => ['Content-Type' => 'application/json']]);
    }


    /**
     * @param array $tattlerBag
     * @return bool
     */
    public function sendPayload(array $tattlerBag): bool
    {
        $request = new Request('POST', $tattlerBag['tattlerUri'], ['connect_timeout' => $tattlerBag['timeout']]);
        $promise = $this->client->sendAsync($request, ['json' => $tattlerBag['payload'] ]);
        $promise->wait(false);

        return true;
    }

    public function syncChannels(array $tattlerBag): ?array
    {
        $request = new Request('POST', $tattlerBag['tattlerUri']);

        $syncChannelsCallback = function (Response $response) {
            try
            {
                $result = json_decode($response->getBody()->getContents());
                return $result->rooms;
            }
            catch(\Exception $e)
            {
				throw new TattlerNetworkException($response->getBody()->getContents());
            }
        };

        $promise = $this->client
            ->sendAsync($request, ['json' => $tattlerBag['payload'] ])
            ->then($syncChannelsCallback);

        return $promise->wait();
    }
}