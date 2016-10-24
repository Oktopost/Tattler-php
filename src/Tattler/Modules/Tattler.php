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

    private function reset()
    {
        $this->targetChannels = [];
        $this->message = null;
        return;
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
     * @param IUser $user
     * @param bool  $unlock
     * @return IChannel[]|[]
     */
    public function getSavedChannels(IUser $user, $unlock = true)
    {
        return $this->accessDAO->loadAllChannels($user->getName(), $unlock);
    }

    /**
     * @param IUser $user
     * @return string[]
     */
    public function getDefaultChannels(IUser $user)
    {
        return [
            $user->getName(),
            Broadcast::BROADCAST_NAME
        ];
    }

    /**
     * @param array $filter
     * @return string[]|[]
     */
    public function getChannels($filter = [])
    {
        $result = $this->syncChannels(array_merge(
            $this->accessDAO->loadAllChannelNames($this->currentUser->getName()),
            $this->getDefaultChannels($this->currentUser)
        ));

        if ($filter)
        {
            return array_unique(array_merge(
                $this->getDefaultChannels($this->currentUser),
                array_values(array_intersect($result, $filter))
            ));
        }

        return $result;
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
        $targetChannels = $this->targetChannels;
        $message = $this->message;

        $this->reset();

        foreach($targetChannels as $channel)
        {
            $message[ 'room' ] = $channel;

            $tattlerBag = [
                'tattlerUri' => $this->getHttpAddress().self::EMIT_ENDPOINT,
                'payload' => [ 'root' => self::$config->Namespace, 'room' => $channel, 'bag' => $message ]
            ];

            if (!Common::network()->sendPayload($tattlerBag)) {
                return false;
            }
        }

        return true;
    }
}