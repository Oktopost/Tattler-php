<?php
namespace Tattler\Channels;


use Closure;
use Ramsey\Uuid\Uuid;
use Tattler\Base\Channels\IUser;


/**
 * Class User
 */
class User implements IUser
{
    /** @var string $name */
    private $name;

    /** @var Closure $nameConverter */
    private $nameConverter;

    /** @var string $socketId */
    private $socketId;


    /**
     * @return Closure
     */
    private function setDefaultConverter()
    {
        return function (...$data) {
            $uuid = Uuid::uuid5(Uuid::NAMESPACE_X500, serialize($data));
            return $uuid->toString();
        };
    }


    /**
     * User constructor.
     */
    public function __construct()
    {
        $this->nameConverter = $this->setDefaultConverter();
    }


    /**
     * @param Closure $callback
     * @return static
     */
    public function setNameConverter(Closure $callback)
    {
        $this->nameConverter = $callback;
        return $this;
    }

    /**
     * @param array $channelNameArgs
     * @return static
     */
    public function setName(...$channelNameArgs)
    {
        $this->name = call_user_func_array($this->nameConverter, $channelNameArgs);
        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param IUser $user
     * @return bool
     */
    public function allow(IUser $user)
    {
        return $this->getName() == $user->getName();
    }

    /**
     * @param IUser $user
     * @return bool
     */
    public function deny(IUser $user)
    {
        return $this->getName() != $user->getName();
    }

    /**
     * @param IUser $user
     * @return bool
     */
    public function isAllowed(IUser $user)
    {
        return $this->allow($user);
    }

    /**
     * @param string $socketId
     * @return static
     */
    public function setSocketId($socketId)
    {
        $this->socketId = $socketId;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getSocketId()
    {
        if (!$this->socketId)
        {
            throw new \Exception('SocketId for current user is not defined');
        }

        return $this->socketId;
    }
}