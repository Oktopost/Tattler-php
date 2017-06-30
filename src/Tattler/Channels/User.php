<?php
namespace Tattler\Channels;


use Tattler\Base\Channels\IUser;
use Tattler\Base\Channels\IChannel;

use Closure;
use Ramsey\Uuid\Uuid;


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


    public function setNameConverter(Closure $callback): IUser
    {
        $this->nameConverter = $callback;
        return $this;
    }

    public function setName(...$channelNameArgs): IChannel
    {
        $this->name = call_user_func_array($this->nameConverter, $channelNameArgs);
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function allow(IUser $user): bool
    {
        return $this->getName() == $user->getName();
    }

    public function deny(IUser $user): bool
    {
        return $this->getName() != $user->getName();
    }

    public function isAllowed(IUser $user): bool
    {
        return $this->allow($user);
    }

    public function setSocketId($socketId): IUser
    {
        $this->socketId = $socketId;
        return $this;
    }

    public function getSocketId(): ?string
    {
        if (!$this->socketId)
        {
            throw new \Exception('SocketId for current user is not defined');
        }

        return $this->socketId;
    }
}