<?php
namespace Tattler\Modules;


use Tattler\Base\Channels\IUser;
use Tattler\Base\Channels\IRoom;
use Tattler\Base\Channels\IChannel;
use Tattler\Base\Modules\ITattlerModule;
use Tattler\Base\Objects\ITattlerMessage;

use Tattler\Channels\Broadcast;

use Tattler\Connectors\Network\GazelleConnector;
use Tattler\Objects\TattlerAccess;
use Tattler\Objects\TattlerConfig;

use Tattler\Connectors\DB\RedisConnector;
use Tattler\Connectors\Network\CurlConnector;
use Tattler\Connectors\Network\GuzzleConnector;
use Tattler\Connectors\Network\HttpfulConnector;

use Firebase\JWT\JWT;


/**
 * @autoload
 */
class TattlerModule implements ITattlerModule
{
	private const ROOMS_ENDPOINT = '/tattler/rooms';
	private const EMIT_ENDPOINT  = '/tattler/emit';

	private const GAZZELE_LIBRARY 		= 'Gazelle\Gazelle';
	private const GUZZLE_LIBRARY  		= 'GuzzleHttp\Client';
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
		
		return $this->config->NetworkConnector->syncChannels($tattlerBag) ?? [];
	}
	
	private function reset(): void
	{
		$this->targetChannels = [];
		$this->message = null;
		return;
	}
	
	private function setDefaultNetworkConnector(): void
	{
		if (class_exists(self::GAZZELE_LIBRARY))
		{
			$this->config->NetworkConnector = new GazelleConnector();
		}
		else if (class_exists(self::GUZZLE_LIBRARY))
		{
			$this->config->NetworkConnector = new GuzzleConnector();
		}
		else if (class_exists(self::HTTPFUL_LIBRARY))
		{
			$this->config->NetworkConnector = new HttpfulConnector();
		}
		else if (function_exists(self::CURL_FUNCTION))
		{
			$this->config->NetworkConnector = new CurlConnector();
		}
		else
		{
			throw new \Exception('Failed to set default Network connector');
		}
	}
	
	private function setDefaultDBConnector(): void
	{
		if (!class_exists(self::PREDIS_LIBRARY))
		{
			throw new \Exception('Failed to set default DB connector');
		}
		
		$this->config->DBConnector = new RedisConnector();
	}
	
	private function afterSetConfig(): void
	{
		if (!$this->config->DBConnector)
			$this->setDefaultDBConnector();
		
		if (!$this->config->NetworkConnector)
			$this->setDefaultNetworkConnector();
		
		$this->accessDAO->setDBConnector($this->config->DBConnector);
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
			$user->getName()
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
				'timeout'	 => $this->config->Timeout,
				'payload'    => [
					'root'   => $this->config->Namespace,
					'secret' => $this->config->Secret,
					'room'   => $channel,
					'bag'    => $bag
				],
			];
			
			$result = $this->config->NetworkConnector->sendPayload($tattlerBag) & $result;
		}
		
		return (bool)$result;
	}
}