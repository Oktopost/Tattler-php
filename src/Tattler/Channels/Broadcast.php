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


    public function setName(...$channelNameArgs): IChannel
    {
        return $this;
    }

    public function getName(): string 
    {
        return self::BROADCAST_NAME;
    }

    public function allow(IUser $user): bool
    {
        return true;
    }

    public function deny(IUser $user): bool
    {
        return false;
    }

    public function isAllowed(IUser $user): bool
    {
        return true;
    }
}