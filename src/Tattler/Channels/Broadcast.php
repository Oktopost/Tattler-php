<?php
namespace Tattler\Channels;


use Tattler\Base\Channels\IChannel;


/**
 * Class Broadcast
 * Channel for all connected users
 */
class Broadcast implements IChannel
{
    const BROADCAST_NAME = 'broadcast';
	
	
	/**
	 * @return static
	 */
    public function setName(...$channelNameArgs)
    {
        return $this;
    }

    public function getName(): string 
    {
        return self::BROADCAST_NAME;
    }
}