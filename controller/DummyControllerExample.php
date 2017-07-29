<?php


use Tattler\SkeletonInit;
use Tattler\Base\Channels\IRoom;
use Tattler\Base\Channels\IUser;
use Tattler\Base\Modules\ITattler;
use Tattler\Objects\TattlerConfig;


/**
 * Class DummyControllerExample
 */
class DummyControllerExample
{
	/** @var ITattler $tattler */
	private $tattler;
	
	
	private function addUserToPrivateRoom(IUser $user): void
	{
		/** @var IRoom $newRoom */
		$newRoom = SkeletonInit::skeleton(IRoom::class);
		$newRoom->setName('privateRoom')->allow($user);
	}
	
	
	public function __construct()
	{
		// Tattler should be already configured by this moment
		$this->tattler = SkeletonInit::skeleton(ITattler::class);
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
		$user = SkeletonInit::skeleton(IUser::class);
		$user
			->setName('current', 'user', 'name', 'with', 'any', 'args')
			->setSocketId($socketId);
		
		$this->tattler->setUser($user);
		
		$this->addUserToPrivateRoom($user);
		
		return ['channels' => $this->tattler->getChannels($channels)];
	}
}