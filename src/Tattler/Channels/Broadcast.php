<?php
namespace Tattler\Channels;


use Tattler\Base\Channels\IChannel;
use Tattler\Base\Channels\IUser;


/**
 * Class Broadcast
 * Channel for all connected users
 */
class Broadcast implements IChannel
{
    const BROADCAST_NAME = 'broadcast';


    /**
     * @param array $channelNameArgs
     * @return static
     */
    public function setName(...$channelNameArgs)
    {
        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return self::BROADCAST_NAME;
    }

    /**
     * @param IUser $user
     * @return bool
     */
    public function allow(IUser $user)
    {
        return true;
    }

    /**
     * @param IUser $user
     * @return bool
     */
    public function deny(IUser $user)
    {
        return false;
    }
}