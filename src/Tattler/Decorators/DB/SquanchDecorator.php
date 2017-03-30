<?php
namespace Tattler\Decorators\DB;


use Objection\LiteObject;
use Objection\Mapper;
use ReflectionClass;
use Squanch\Base\ICachePlugin;

use Tattler\Objects\TattlerAccess;
use Tattler\Base\Decorators\IDBDecorator;


class SquanchDecorator implements IDBDecorator
{
	/** @var ICachePlugin */
	private $client;
	
	private $bucket;
	
	
	/**
	 * @param LiteObject $object
	 * @return string
	 */
	private function getClassShortName($object)
	{
		$reflection = new ReflectionClass($object);
		return $reflection->getShortName();
	}
	
	
	/**
	 * @return ICachePlugin
	 */
	public function getConnection()
	{
		return $this->client;
	}
	
	/**
	 * @param TattlerAccess $object
	 * @return string
	 */
	private function getAccessObjectKey(TattlerAccess $object)
	{
		return $this->getClassShortName($object).':'.$object->UserToken;
	}
	
	
	/**
	 * @param ICachePlugin $squanch
	 * @param string $bucket
	 */
	public function __construct(ICachePlugin $squanch, $bucket = 'tattler')
	{
		$this->client = $squanch;
		$this->bucket = $bucket;
	}
	
	
	public function insertAccess(TattlerAccess $access, $ttl)
	{
		return $this->client->set()
			->setKey($this->getAccessObjectKey($access))
			->setData(Mapper::getJsonFor($access))
			->setBucket($this->bucket)
			->setTTL($ttl)
			->execute();
	}
	
	/**
	 * @param TattlerAccess $access
	 * @param int $newTTL
	 * @return mixed
	 */
	public function updateAccessTTL(TattlerAccess $access, $newTTL)
	{
		return $this->client->has()
			->byKey($this->getAccessObjectKey($access))
			->byBucket($this->bucket)
			->resetTTL($newTTL)
			->execute();
	}
	
	/**
	 * @param TattlerAccess $access
	 * @return bool
	 */
	public function accessExists(TattlerAccess $access)
	{
		return $this->client->has()
			->byKey($this->getAccessObjectKey($access))
			->byBucket($this->bucket)
			->execute();
	}
	
	/**
	 * @param TattlerAccess $access
	 * @return bool
	 */
	public function deleteAccess(TattlerAccess $access)
	{
		return $this->client->delete()
			->byKey($this->getAccessObjectKey($access))
			->byBucket($this->bucket)
			->execute();
	}
	
	/**
	 * @param string $userToken
	 * @param bool $unlock
	 * @return bool|TattlerAccess[]
	 */
	public function loadAllChannels($userToken, $unlock = true)
	{
		$tmpAccess = new TattlerAccess();
		$tmpAccess->UserToken = $userToken;
		$data = $this->client->get()->byBucket($this->getAccessObjectKey($tmpAccess))->asCollection();
		
		if (!$data)
			return false;
		
		$result = $data->asLiteObjects(TattlerAccess::class);
		$locked = [];
		
		/** @var TattlerAccess $value */
		foreach($result as $key=>$value)
		{
			if ($value->IsLocked)
			{
				$locked[] = $value;
				unset($result[$key]);
			}
		}
		
		if ($unlock) {
			foreach ($locked as $value) {
				$this->unlock($value);
			}
		}
		
		return $result;
	}
	
	/**
	 * @param TattlerAccess $access
	 * @return bool
	 */
	public function lock(TattlerAccess $access)
	{
		$access->IsLocked = true;
		
		return $this->client->set()
			->updateOnly()
			->setKey($this->getAccessObjectKey($access))
			->setData(Mapper::getJsonFor($access))
			->setBucket($this->bucket)
			->execute();
	}
	
	/**
	 * @param TattlerAccess $access
	 * @return bool
	 */
	public function unlock(TattlerAccess $access)
	{
		$access->IsLocked = false;
		
		return $this->client->set()
			->updateOnly()
			->setKey($this->getAccessObjectKey($access))
			->setData(Mapper::getJsonFor($access))
			->setBucket($this->bucket)
			->execute();
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