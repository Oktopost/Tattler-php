<?php
namespace Tattler\DAL;


use Tattler\Base\Channels\IRoom;
use Tattler\Base\DAL\ITattlerAccessDAO;
use Tattler\Base\Connectors\IDBConnector;
use Tattler\Channels\Room;
use Tattler\Objects\TattlerAccess;


class TattlerAccessDAO implements ITattlerAccessDAO
{
	private const DATA_TTL = 604800; // week
	
	
	/** @var IDBConnector $connector */
	private $connector;
	
	
	public function setDBConnector(IDBConnector $dbConnector): void
	{
		$this->connector = $dbConnector;
		$this->removeOld();
	}
	
	
	public function allow(TattlerAccess $access): bool
	{
		$exists = $this->exists($access);
		
		if ($exists)
		{
			$this->connector->unlock($access);
			
			return $this->connector->updateAccessTTL($access, self::DATA_TTL);
		}
		else
		{
			return $this->connector->insertAccess($access, self::DATA_TTL);
		}
	}
	
	public function exists(TattlerAccess $access): bool
	{
		return $this->connector->accessExists($access);
	}
	
	public function deny(TattlerAccess $access): bool
	{
		if (!$this->exists($access))
			return true;
		
		return $this->connector->deleteAccess($access);
	}
	
	public function loadAllChannels(string $userToken, bool $unlock = true): array
	{
		$result = [];
		
		/** @var TattlerAccess[] $query */
		$query = $this->connector->loadAllChannels($userToken, $unlock);
		
		if (!$query)
			return $result;
		
		$keepAliveAfter = strtotime('now') - self::DATA_TTL;
		
		/** @var TattlerAccess $item */
		foreach ($query as $item)
		{
			if (strtotime($item->Modified) < $keepAliveAfter)
			{
				$this->connector->deleteAccess($item);
				continue;
			}
			
			/** @var IRoom $room */
			$room = new Room();
			$room->setName($item->Channel);
			$result[] = $room;
		}
		
		return $result;
	}
	
	public function loadAllChannelNames(string $userToken, bool $unlock = true): array
	{
		$result = [];
		
		/** @var TattlerAccess[] $query */
		$query = $this->connector->loadAllChannels($userToken, $unlock);
		
		if (!$query)
			return $result;
		
		$keepAliveAfter = strtotime('now') - self::DATA_TTL;
		
		foreach ($query as $item)
		{
			if (strtotime($item->Modified) < $keepAliveAfter)
			{
				$this->connector->deleteAccess($item);
				continue;
			}
			
			$result[] = $item->Channel;
		}
		
		return $result;
	}
	
	public function lock(TattlerAccess $access): bool
	{
		return $this->connector->lock($access);
	}
	
	public function removeOld(): bool
	{
		return $this->connector->removeGarbage(self::DATA_TTL);
	}
}