<?php
namespace Tattler\Channels;


use Tattler\Objects\TattlerAccess;
use Tattler\Base\Channels\IRoom;
use Tattler\Base\Channels\IUser;


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

        return $access;
    }


    /**
     * @param array $channelNameArgs
     * @return static
     */
    public function setName(...$channelNameArgs)
    {
        $this->name = implode(':', $channelNameArgs);
        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        if (!$this->name)
        {
            throw new \Exception('Room without name');
        }

        return $this->name;
    }

    /**
     * @param IUser $user
     * @return bool
     */
    public function allow(IUser $user)
    {
        return $this->accessDAO->allow($this->getAccessObject($user->getName()));
    }

    /**
     * @param IUser $user
     * @return bool
     */
    public function deny(IUser $user)
    {
        return $this->accessDAO->deny($this->getAccessObject($user->getName()));
    }

    /**
     * @param IUser $user
     * @return bool
     */
    public function isAllowed(IUser $user)
    {
        return $this->accessDAO->exists($this->getAccessObject($user->getName()));
    }
}