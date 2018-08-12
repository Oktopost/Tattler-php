<?php
namespace Tattler\DAL;


use Tattler\Base\Channels\IRoom;
use Tattler\Base\DAL\ITattlerAccessDAO;
use Tattler\Base\Decorators\IDBDecorator;
use Tattler\Channels\Room;
use Tattler\Objects\TattlerAccess;


class TattlerAccessDAO implements ITattlerAccessDAO
{
	private const DATA_TTL = 604800; // week
	
	
	/** @var IDBDecorator $decorator */
	private $decorator;
	
	
	public function setDBDecorator(IDBDecorator $dbDecorator): void
	{
		$this->decorator = $dbDecorator;
		$this->removeOld();
	}
	
	
	public function allow(TattlerAccess $access): bool
	{
		$exists = $this->exists($access);
		
		if ($exists)
		{
			$this->decorator->unlock($access);
			
			return $this->decorator->updateAccessTTL($access, self::DATA_TTL);
		}
		else
		{
			return $this->decorator->insertAccess($access, self::DATA_TTL);
		}
	}
	
	public function exists(TattlerAccess $access): bool
	{
		return $this->decorator->accessExists($access);
	}
	
	public function deny(TattlerAccess $access): bool
	{
		if (!$this->exists($access))
			return true;
		
		return $this->decorator->deleteAccess($access);
	}
	
	public function loadAllChannels(string $userToken, bool $unlock = true): array
	{
		$result = [];
		
		/** @var TattlerAccess[] $query */
		$query = $this->decorator->loadAllChannels($userToken, $unlock);
		
		if (!$query)
			return $result;
		
		$keepAliveAfter = strtotime('now') - self::DATA_TTL;
		
		/** @var TattlerAccess $item */
		foreach ($query as $item)
		{
			if (strtotime($item->Modified) < $keepAliveAfter)
			{
				$this->decorator->deleteAccess($item);
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
		$query = $this->decorator->loadAllChannels($userToken, $unlock);
		
		if (!$query)
			return $result;
		
		$keepAliveAfter = strtotime('now') - self::DATA_TTL;
		
		foreach ($query as $item)
		{
			if (strtotime($item->Modified) < $keepAliveAfter)
			{
				$this->decorator->deleteAccess($item);
				continue;
			}
			
			$result[] = $item->Channel;
		}
		
		return $result;
	}
	
	public function lock(TattlerAccess $access): bool
	{
		return $this->decorator->lock($access);
	}
	
	public function removeOld(): bool
	{
		return $this->decorator->removeGarbage(self::DATA_TTL);
	}
}