<?php
namespace Tattler\Channels;


use Tattler\Base\Channels\IRoom;
use Tattler\Base\Channels\IUser;

use Tattler\Objects\TattlerAccess;


/**
 * @autoload
 */
class Room implements IRoom
{
    /**
     * @autoload
     * @var \Tattler\Base\DAL\ITattlerAccessDAO $accessDAO
     */
    private $accessDAO;

    private $name;


    /**
     * @param $userToken
     * @return TattlerAccess
     */
    private function getAccessObject($userToken)
    {
        $access = new TattlerAccess();
        $access->Channel = $this->getName();
        $access->UserToken = $userToken;
        $access->IsLocked = false;

        return $access;
    }
	
    
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

    public function allow(IUser $user): bool
    {
        return $this->accessDAO->allow($this->getAccessObject($user->getName()));
    }

    public function deny(IUser $user): bool
    {
        return $this->accessDAO->deny($this->getAccessObject($user->getName()));
    }

    public function isAllowed(IUser $user): bool
    {
        return $this->accessDAO->exists($this->getAccessObject($user->getName()));
    }

    public function lock(IUser $user): bool
    {
        return $this->accessDAO->lock($this->getAccessObject($user->getName()));
    }
}