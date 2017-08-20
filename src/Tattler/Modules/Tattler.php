<?php
namespace Tattler\Modules;


use Tattler\SkeletonInit;
use Tattler\Channels\Broadcast;
use Tattler\Objects\TattlerConfig;

use Tattler\Base\Channels\IUser;
use Tattler\Base\Channels\IChannel;
use Tattler\Base\Modules\ITattler;
use Tattler\Base\Objects\ITattlerMessage;
use Tattler\Base\Decorators\INetworkDecorator;

use Firebase\JWT\JWT;


/**
 * @autoload
 */
class Tattler implements ITattler
{
	private const ROOMS_ENDPOINT = '/tattler/rooms';
	private const EMIT_ENDPOINT  = '/tattler/emit';
	
	
	/** @var TattlerConfig $config */
	private static $config;
	
	/**
	 * @autoload
	 * @var \Tattler\Base\DAL\ITattlerAccessDAO $accessDAO
	 */
	private $accessDAO;
	
	/** @var array $targetChannels */
	private $targetChannels = [];
	
	/** @var IUser $currentUser */
	private $currentUser;
	
	/** @var array $message */
	private $message;
	
	
	private function getApiAddress(): string
	{
		return self::$config->ApiAddress;
	}
	
	private function syncChannels(array $channels): array
	{
		$userToken = $this->currentUser->getName();
		$socketId = $this->currentUser->getSocketId();
		
		$tattlerBag = [
			'tattlerUri' => $this->getApiAddress() . self::ROOMS_ENDPOINT,
			'payload'    => [
				'client' => ['socketId' => $socketId, 'sessionId' => $userToken],
				'secret' => self::$config->Secret,
				'rooms'  => implode(',', $channels),
				'root'   => self::$config->Namespace
			]
		];
		
		/** @var INetworkDecorator $result */
		$network = SkeletonInit::skeleton(INetworkDecorator::class);
		return $network->syncChannels($tattlerBag) ?? [];
	}
	
	private function reset(): void
	{
		$this->targetChannels = [];
		$this->message = null;
		return;
	}
	
	public function setConfig(TattlerConfig $config): ITattler
	{
		self::$config = $config;
		return $this;
	}
	
	public function setConfigValue(string $key, string $value): bool
	{
		if (!isset(self::$config->{$key}))
		{
			return false;
		}
	
		self::$config->{$key} = $value;
		
		return true;
	}
	
	public function getConfig()
	{
		return self::$config;
	}
	
	public function getWsAddress(): string
	{
		return self::$config->WsAddress;
	}
	
	public function getJWTToken(): string
	{
		$secret = self::$config->Secret;
		$ttl = (int)self::$config->TokenTTL;
		
		return JWT::encode(
			[
				'r'   => mt_rand(),
				'exp' => strtotime('now') + $ttl
			],
			$secret
		);
	}
	
	public function getSavedChannels(IUser $user, bool $unlock = true): array
	{
		return $this->accessDAO->loadAllChannels($user->getName(), $unlock);
	}
	
	public function getDefaultChannels(IUser $user): array
	{
		return [
			$user->getName(),
			Broadcast::BROADCAST_NAME
		];
	}
	
	public function getChannels(?array $filter = []): array
	{
		$result = $this->syncChannels(array_unique(array_merge(
			$this->accessDAO->loadAllChannelNames($this->currentUser->getName()),
			$this->getDefaultChannels($this->currentUser)
		)));
		
		if ($filter)
		{
			return array_unique(array_merge(
				$this->getDefaultChannels($this->currentUser),
				array_values(array_intersect($result, $filter))
			));
		}
		
		return $result;
	}
	
	public function setUser(IUser $user): ITattler
	{
		$this->currentUser = $user;
		return $this;
	}
	
	public function broadcast(): ITattler
	{
		$this->targetChannels[] = Broadcast::BROADCAST_NAME;
		return $this;
	}
	
	public function room(IChannel $room): ITattler
	{
		$this->targetChannels[] = $room->getName();
		return $this;
	}
	
	public function user(IUser $user): ITattler
	{
		$this->targetChannels[] = $user->getName();
		return $this;
	}
	
	public function message(ITattlerMessage $message): ITattler
	{
		$this->message = $message->toArray();
		return $this;
	}
	
	public function say(): bool
	{
		$targetChannels = $this->targetChannels;
		$bag = $this->message;
		$bag['id'] = uniqid();
		
		$this->reset();
		
		$result = true;
		
		$network = SkeletonInit::skeleton(INetworkDecorator::class);
		
		foreach ($targetChannels as $channel)
		{
			$bag['room'] = $channel;
			
			$tattlerBag = [
				'tattlerUri' => $this->getApiAddress() . self::EMIT_ENDPOINT,
				'payload'    => [
					'root'   => self::$config->Namespace,
					'secret' => self::$config->Secret,
					'room'   => $channel,
					'bag'    => $bag
				],
			];
			
			$result = $network->sendPayload($tattlerBag) & $result;
		}
		
		return (bool)$result;
	}
}