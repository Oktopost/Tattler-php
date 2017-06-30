<?php
namespace Tattler\Decorators\DB;


use Tattler\Objects\TattlerAccess;
use Tattler\Base\Decorators\IDBDecorator;

use Squanch\Base\ICachePlugin;


class SquanchDecorator implements IDBDecorator
{
	/** @var ICachePlugin */
	private $client;
	
	/** @var string  */
	private $bucket;
	
	
	public function __construct(ICachePlugin $squanch, string $bucketName = 'tattler-php')
	{
		$this->client = $squanch;
		$this->bucket = $bucketName;
	}
	
	
	public function insertAccess(TattlerAccess $access, int $ttl): bool
	{
		$items = $this->client
			->get($access->UserToken, $this->bucket)
			->asLiteObjects(TattlerAccess::class);
		
		if (!$items)
		{
			$items = [$access];
		}
		else
		{
			$exists = false;
			/** @var TattlerAccess $item */
			foreach ($items as $key=>$item)
			{
				if ($item->Channel === $access->Channel)
				{
					$items[$key] = $access;
					$exists = true;
					break;
				}
			}
			
			if (!$exists) $items[] = $access;
		}
		
		return $this->client->set($access->UserToken, $items, $this->bucket)
			->setTTL($ttl)
			->save();
	}
	
	public function updateAccessTTL(TattlerAccess $access, int $newTTL): bool
	{
		return $this->insertAccess($access, $newTTL);
	}
	
	public function accessExists(TattlerAccess $access): bool
	{
		$items = $this->client->get()
			->byKey($access->UserToken)
			->byBucket($this->bucket)
			->asLiteObjects(TattlerAccess::class);
		
		if (!$items) return false;
		
		/** @var TattlerAccess $item */
		foreach ($items as $item)
		{
			if ($item->Channel === $access->Channel)
			{
				return true;
			}
		}
		
		return false;
	}
	
	public function deleteAccess(TattlerAccess $access): bool
	{
		$items = $this->client->get($access->UserToken, $this->bucket)->asLiteObjects(TattlerAccess::class);
		
		if (!$items) return true;
		
		$found = false;
		
		/** @var TattlerAccess $item */
		foreach ($items as $key=>$item)
		{
			if ($item->Channel == $access->Channel)
			{
				unset($items[$key]);
				$found = true;
				break;
			}
		}
		
		if ($found)
		{
			$this->client->set($access->UserToken, $items, $this->bucket)->update();
		}
		
		return true;
	}
	
	public function loadAllChannels(string $userToken, bool $unlock = true): array
	{
		$data = $this->client->get()
			->byKey($userToken)
			->byBucket($this->bucket)
			->asLiteObjects(TattlerAccess::class);
		
		if (!$data)
			return [];
		
		$locked = [];
		
		/** @var TattlerAccess $value */
		foreach($data as $key=>$value)
		{
			if ($value->IsLocked)
			{
				$locked[] = $value;
				unset($data[$key]);
			}
		}
		
		if ($unlock) {
			foreach ($locked as $value) {
				$this->unlock($value);
			}
		}
		
		return $data;
	}
	
	public function lock(TattlerAccess $access): bool
	{
		$access->IsLocked = true;
		
		return $this->insertAccess($access, -1);
	}
	
	public function unlock(TattlerAccess $access): bool
	{
		$access->IsLocked = false;
		
		return $this->insertAccess($access, -1);
	}
	
	public function removeGarbage(int $maxTTL): bool
	{
		return true;
	}
}