<?php
namespace Tattler\Modules;


use Tattler\Base\Channels\IUser;
use Tattler\Base\Channels\IRoom;
use Tattler\Base\Channels\IChannel;
use Tattler\Base\Modules\ITattlerModule;
use Tattler\Base\Objects\ITattlerMessage;

use Tattler\Channels\Broadcast;

use Tattler\Objects\TattlerAccess;
use Tattler\Objects\TattlerConfig;

use Tattler\Decorators\DB\RedisDecorator;
use Tattler\Decorators\Network\CurlDecorator;
use Tattler\Decorators\Network\GuzzleDecorator;
use Tattler\Decorators\Network\HttpfulDecorator;

use Firebase\JWT\JWT;


/**
 * @autoload
 */
class TattlerModule implements ITattlerModule
{
	private const ROOMS_ENDPOINT = '/tattler/rooms';
	private const EMIT_ENDPOINT  = '/tattler/emit';

	private const GUZZLE_LIBRARY		= 'GuzzleHttp\Client';
	private const HTTPFUL_LIBRARY		= 'Httpful\Request';
	private const CURL_FUNCTION			= 'curl_init';
	
	private const PREDIS_LIBRARY		= 'Predis\Client';
	
	
	/** @var TattlerConfig $config */
	private $config;
	
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
		return $this->config->ApiAddress;
	}
	
	private function syncChannels(array $channels): array
	{
		$userToken = $this->currentUser->getName();
		$socketId = $this->currentUser->getSocketId();
		
		$tattlerBag = [
			'tattlerUri' => $this->getApiAddress() . self::ROOMS_ENDPOINT,
			'payload'    => [
				'client' => ['socketId' => $socketId, 'sessionId' => $userToken],
				'secret' => $this->config->Secret,
				'rooms'  => implode(',', $channels),
				'root'   => $this->config->Namespace
			]
		];
		
		return $this->config->NetworkDecorator->syncChannels($tattlerBag) ?? [];
	}
	
	private function reset(): void
	{
		$this->targetChannels = [];
		$this->message = null;
		return;
	}
	
	private function setDefaultNetworkDecorator(): void
	{
		if (class_exists(self::GUZZLE_LIBRARY))
		{
			$this->config->NetworkDecorator = new GuzzleDecorator();
		}
		else if (class_exists(self::HTTPFUL_LIBRARY))
		{
			$this->config->NetworkDecorator = new HttpfulDecorator();
		}
		else if (function_exists(self::CURL_FUNCTION))
		{
			$this->config->NetworkDecorator = new CurlDecorator();
		}
		else
		{
			throw new \Exception('Failed to set default Network decorator');
		}
	}
	
	private function setDefaultDBDecorator(): void
	{
		
		if (!class_exists(self::PREDIS_LIBRARY))
		{
			throw new \Exception('Failed to set default DB decorator');
		}
		
		$this->config->DBDecorator = new RedisDecorator();
	}
	
	private function afterSetConfig(): void
	{
		if (!$this->config->DBDecorator)
			$this->setDefaultDBDecorator();
		
		if (!$this->config->NetworkDecorator)
			$this->setDefaultNetworkDecorator();
		
		$this->accessDAO->setDBDecorator($this->config->DBDecorator);
	}
	
	private function getAccessObject($roomName, $userToken)
	{
		$access = new TattlerAccess();
		$access->Channel = $roomName;
		$access->UserToken = $userToken;
		
		return $access;
	}

	
	public function setConfig(TattlerConfig $config): ITattlerModule
	{
		$this->config = $config;
		
		$this->afterSetConfig();
		
		return $this;
	}
	
	public function setConfigValue(string $key, $value): bool
	{
		if (!isset($this->config->{$key}))
		{
			return false;
		}
	
		$this->config->{$key} = $value;
		
		return true;
	}
	
	public function getConfig()
	{
		return $this->config;
	}
	
	public function getWsAddress(): string
	{
		return $this->config->WsAddress;
	}
	
	public function getJWTToken(): string
	{
		$secret = $this->config->Secret;
		$ttl = (int)$this->config->TokenTTL;
		
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
			return array_unique(array_values(array_intersect($result, $filter)));
		}
		
		return $result;
	}
	
	public function setUser(IUser $user): ITattlerModule
	{
		$this->currentUser = $user;
		return $this;
	}
	
	public function allowAccess(IRoom $room, ?IUser $user = null): bool
	{
		if (!$user)
			$user = $this->currentUser;
		
		return $this->accessDAO->allow($this->getAccessObject($room->getName(), $user->getName()));
	}
	
	public function denyAccess(IRoom $room, ?IUser $user = null): bool
	{
		if (!$user)
			$user = $this->currentUser;
		
		return $this->accessDAO->deny($this->getAccessObject($room->getName(), $user->getName()));
	}
	
	public function isAllowed(IRoom $room, ?IUser $user = null): bool
	{
		if (!$user)
			$user = $this->currentUser;
		
		return $this->accessDAO->exists($this->getAccessObject($room->getName(), $user->getName()));
	}
	
	
	public function broadcast(): ITattlerModule
	{
		$this->targetChannels[] = Broadcast::BROADCAST_NAME;
		return $this;
	}
	
	public function room(IChannel $room): ITattlerModule
	{
		$this->targetChannels[] = $room->getName();
		return $this;
	}
	
	public function user(IUser $user): ITattlerModule
	{
		$this->targetChannels[] = $user->getName();
		return $this;
	}
	
	public function message(ITattlerMessage $message): ITattlerModule
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
		
		foreach ($targetChannels as $channel)
		{
			$bag['room'] = $channel;
			
			$tattlerBag = [
				'tattlerUri' => $this->getApiAddress() . self::EMIT_ENDPOINT,
				'payload'    => [
					'root'   => $this->config->Namespace,
					'secret' => $this->config->Secret,
					'room'   => $channel,
					'bag'    => $bag
				],
			];
			
			$result = $this->config->NetworkDecorator->sendPayload($tattlerBag) & $result;
		}
		
		return (bool)$result;
	}
}