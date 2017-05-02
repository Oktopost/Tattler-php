<?php
namespace Tattler\Decorators\DB;


use Tattler\Objects\TattlerAccess;
use Tattler\Base\Decorators\IDBDecorator;

use Squanch\Base\ICachePlugin;


class SquanchDecorator implements IDBDecorator
{
	/** @var ICachePlugin */
	private $client;
	
	private $bucket;
	
	
	/**
	 * @param ICachePlugin $squanch
	 * @param string $bucketName
	 */
	public function __construct(ICachePlugin $squanch, $bucketName = 'tattler-php')
	{
		$this->client = $squanch;
		$this->bucket = $bucketName;
	}
	
	
	/**
	 * @param TattlerAccess $access
	 * @param int $ttl
	 * @return bool
	 */
	public function insertAccess(TattlerAccess $access, $ttl)
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
	
	/**
	 * @param TattlerAccess $access
	 * @param int $newTTL
	 * @return bool
	 */
	public function updateAccessTTL(TattlerAccess $access, $newTTL)
	{
		return $this->insertAccess($access, $newTTL);
	}
	
	/**
	 * @param TattlerAccess $access
	 * @return bool
	 */
	public function accessExists(TattlerAccess $access)
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
	
	/**
	 * @param TattlerAccess $access
	 * @return bool
	 */
	public function deleteAccess(TattlerAccess $access)
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
	
	/**
	 * @param string $userToken
	 * @param bool $unlock
	 * @return TattlerAccess[]|bool
	 */
	public function loadAllChannels($userToken, $unlock = true)
	{
		$data = $this->client->get()
			->byKey($userToken)
			->byBucket($this->bucket)
			->asLiteObjects(TattlerAccess::class);
		
		if (!$data)
			return false;
		
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
	
	/**
	 * @param TattlerAccess $access
	 * @return bool
	 */
	public function lock(TattlerAccess $access)
	{
		$access->IsLocked = true;
		
		return $this->insertAccess($access, -1);
	}
	
	/**
	 * @param TattlerAccess $access
	 * @return bool
	 */
	public function unlock(TattlerAccess $access)
	{
		$access->IsLocked = false;
		
		return $this->insertAccess($access, -1);
	}
	
	/**
	 * @param int $maxTTL
	 * @return bool
	 */
	public function removeGarbage($maxTTL)
	{
		return true;
	}
}