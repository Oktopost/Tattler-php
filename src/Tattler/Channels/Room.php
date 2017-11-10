<?php
namespace Tattler\Channels;


use Tattler\Base\Channels\IRoom;


class Room implements IRoom
{
    private $name;

	
	/**
	 * @return static
	 */
    public function setName(...$channelNameArgs)
    {
        $this->name = implode(':', $channelNameArgs);
        return $this;
    }

    public function getName(): string
    {
        if (!$this->name)
        {
            throw new \Exception('Room without name');
        }

        return $this->name;
    }
}