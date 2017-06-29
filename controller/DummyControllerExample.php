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
	
	
	public function __construct()
	{
		$this->tattler = SkeletonInit::skeleton(ITattler::class);
		$config = new TattlerConfig();
//		$config->WsAddress = 'wss://domain.tld/...';
//      $config->ApiAddress = 'https://domain.tld/...';
//		$config->...
		$this->tattler->setConfig($config);
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
		return [
			'token' => $this->tattler->getJWTToken()
		];
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
		$user->setName('current', 'user', 'name', 'with', 'any', 'args')
			->setSocketId($socketId);
		
		$this->tattler->setUser($user);
		
		/** @var IRoom $newRoom */
		$newRoom = SkeletonInit::skeleton(IRoom::class);
		$newRoom->setName('newRoom')->allow($user);
		
		return ['channels' => $this->tattler->getChannels($channels)];
	}
}