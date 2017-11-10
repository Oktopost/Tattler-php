<?php
namespace Tattler\Channels;


use Tattler\Base\Channels\IUser;

use Ramsey\Uuid\Uuid;


class User implements IUser
{
    /** @var string $name */
    private $name;

    /** @var \Closure $nameConverter */
    private $nameConverter;

    /** @var string $socketId */
    private $socketId;


    /**
     * @return \Closure
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


    public function setNameConverter(\Closure $callback): IUser
    {
        $this->nameConverter = $callback;
        return $this;
    }
	
	/**
	 * @return static
	 */
    public function setName(...$channelNameArgs)
    {
        $this->name = call_user_func_array($this->nameConverter, $channelNameArgs);
        return $this;
    }

    public function getName(): string
    {
    	if (!$this->name && $this->socketId)
		{
			$this->setName($this->socketId);
		}
		
		if (!$this->name)
		{
			throw new \Exception('User without name');
		}
		
        return $this->name;
    }

    public function setSocketId($socketId): IUser
    {
        $this->socketId = $socketId;
        return $this;
    }

    public function getSocketId(): string
    {
        if (!$this->socketId)
        {
            throw new \Exception('SocketId for this user is not defined');
        }

        return $this->socketId;
    }
}