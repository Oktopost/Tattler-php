<?php
use Tattler\Tattler;

use Tattler\Base\Channels\IRoom;
use Tattler\Base\Channels\IUser;
use Tattler\Base\Modules\ITattlerModule;

use Tattler\Objects\TattlerConfig;

use Tattler\Channels\Room;
use Tattler\Channels\User;


/**
 * Class DummyControllerExample
 */
class DummyControllerExample
{
	/** @var ITattlerModule $tattler */
	private $tattler;
	
	
	private function addUserToPrivateRoom(IUser $user): void
	{
		/** @var IRoom $newRoom */
		$newRoom = new Room();
		$newRoom->setName('privateRoom');
		$this->tattler->allowAccess($newRoom, $user);
	}
	
	
	public function __construct()
	{
		$config = new TattlerConfig();
		$this->tattler = Tattler::getInstance($config);
	}
	
	/**
	 * @return array
	 */
	public function getWs()
	{
		return ['ws' => $this->tattler->getWsAddress()];
	}
	
	
	public function getAuth()
	{
		return ['token' => $this->tattler->getJWTToken()];
	}
	
	/**
	 * @param string $socketId
	 * @param null|array $channels
	 * @return array
	 */
	public function getChannels($socketId, $channels = null)
	{
		/** @var IUser $user */
		$user = new User();
		$user
			->setName('current', 'user', 'name', 'with', 'any', 'args')
			->setSocketId($socketId);
		
		$this->tattler->setUser($user);
		
		$this->addUserToPrivateRoom($user);
		
		return ['channels' => $this->tattler->getChannels($channels)];
	}
}