<?php
namespace Tattler\Modules;

use Tattler\Base\Channels\IChannel;
use Tattler\Base\Channels\IUser;
use Tattler\Base\DAL\ITattlerAccessDAO;
use Tattler\Base\Modules\ITattler;
use Tattler\Base\Objects\ITattlerMessage;
use Tattler\Channels\Broadcast;
use Tattler\Common;
use Tattler\Objects\TattlerConfig;


/**
 * Class Tattler
 */
class Tattler implements ITattler
{
    const ROOMS_ENDPOINT = '/tattler/rooms';

    const EMIT_ENDPOINT = '/tattler/emit';


    /** @var TattlerConfig $config */
    private static $config;

    /** @var ITattlerAccessDAO $accessDAO */
    private $accessDAO;

    /** @var array $targetChannels */
    private $targetChannels = [];

    /** @var IUser $currentUser */
    private $currentUser;

    /** @var array $message */
    private $message;


    /**
     * @param array $channels
     * @return array
     */
    private function syncChannels($channels)
    {
        $userToken = $this->currentUser->getName();
        $socketId = $this->currentUser->getSocketId();

        $tattlerBag = [
            'tattlerUri' => $this->getHttpAddress().self::ROOMS_ENDPOINT,
            'payload' => [
                'client' => [ 'socketId' => $socketId, 'sessionId' => $userToken ],
                'rooms' => implode(',', $channels),
                'root' => self::$config->Namespace
            ]
        ];

        return Common::network()->syncChannels($tattlerBag);
    }


    /**
     * Tattler constructor.
     * @param ITattlerAccessDAO $accessDAO
     */
    public function __construct(ITattlerAccessDAO $accessDAO)
    {
        $this->accessDAO = $accessDAO;
    }

    /**
     * @param TattlerConfig $config
     * @return static
     */
    public function setConfig(TattlerConfig $config)
    {
        self::$config = $config;
        return $this;
    }

    /**
     * @return string
     */
    public function getWsAddress()
    {
        return 'w' . (self::$config->Secure ? 's' : '') .'s://'.self::$config->Server;
    }

    /**
     * @return string
     */
    public function getHttpAddress()
    {
        return 'http' . (self::$config->Secure ? 's' : '') .'://'.self::$config->Server;
    }

    /**
     * @return string[]
     */
    public function getChannels()
    {
        $channels = $this->accessDAO->loadAllChannelsNames($this->currentUser->getName());
        $channels[] = $this->currentUser->getName();
        $channels[] = Broadcast::BROADCAST_NAME;

        return $this->syncChannels($channels);
    }

    /**
     * @param IUser $user
     * @return static
     */
    public function setUser(IUser $user)
    {
        $this->currentUser = $user;
        return $this;
    }

    /**
     * @return static
     */
    public function broadcast()
    {
        $this->targetChannels[] = Broadcast::BROADCAST_NAME;
        return $this;
    }

    /**
     * @param IChannel $room
     * @return static
     */
    public function room(IChannel $room)
    {
        $this->targetChannels[] = $room->getName();
        return $this;
    }

    /**
     * @param IUser $user
     * @return static
     */
    public function user(IUser $user)
    {
        $this->targetChannels[] = $user->getName();
        return $this;
    }

    /**
     * @param ITattlerMessage $message
     * @return static
     */
    public function message(ITattlerMessage $message)
    {
        $this->message = $message->toArray();
        return $this;
    }

    /**
     * @return bool
     */
    public function say()
    {
        $result = true;

        foreach($this->targetChannels as $channel)
        {
            $data = $this->message;
            $data[ 'room' ] = $channel;

            $tattlerBag = [
                'tattlerUri' => $this->getHttpAddress().self::EMIT_ENDPOINT,
                'payload' => [ 'root' => self::$config->Namespace, 'room' => $channel, 'bag' => $data ]
            ];

            if (!Common::network()->sendPayload($tattlerBag))
                $result = false;
        }

        return $result;
    }
}