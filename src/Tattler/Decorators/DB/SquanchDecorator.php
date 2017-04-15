<?php
namespace Tattler\Decorators\DB;


use Objection\Mapper;
use Objection\LiteObject;

use ReflectionClass;
use Squanch\Base\ICachePlugin;

use Tattler\Objects\TattlerAccess;
use Tattler\Base\Decorators\IDBDecorator;


class SquanchDecorator implements IDBDecorator
{
	/** @var ICachePlugin */
	private $client;
	
	
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
	 * @param TattlerAccess $object
	 * @return string
	 */
	private function getBucketName(TattlerAccess $object)
	{
		return $this->getClassShortName($object).':'.$object->UserToken;
	}
	
	
	/**
	 * @param ICachePlugin $squanch
	 * @param string $bucket
	 */
	public function __construct(ICachePlugin $squanch)
	{
		$this->client = $squanch;
	}
	
	
	/**
	 * @return ICachePlugin
	 */
	public function getConnection()
	{
		return $this->client;
	}
	
	
	public function insertAccess(TattlerAccess $access, $ttl)
	{
		return $this->client->set()
			->setKey($access->Channel)
			->setData(Mapper::getJsonFor($access))
			->setBucket($this->getBucketName($access))
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
		return $this->client->set()
			->updateOnly()
			->setKey($access->Channel)
			->setBucket($this->getBucketName($access))
			->setTTL($newTTL)
			->setData($access)
			->execute();
	}
	
	/**
	 * @param TattlerAccess $access
	 * @return bool
	 */
	public function accessExists(TattlerAccess $access)
	{
		return $this->client->has()
			->byKey($access->Channel)
			->byBucket($this->getBucketName($access))
			->execute();
	}
	
	/**
	 * @param TattlerAccess $access
	 * @return bool
	 */
	public function deleteAccess(TattlerAccess $access)
	{
		return $this->client->delete()
			->byKey($access->Channel)
			->byBucket($this->getBucketName($access))
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
		$data = $this->client->get()->byBucket($this->getBucketName($tmpAccess))->asCollection();
		
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
			->setKey($access->Channel)
			->setData(Mapper::getJsonFor($access))
			->setBucket($this->getBucketName($access))
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
			->setKey($access->Channel)
			->setData(Mapper::getJsonFor($access))
			->setBucket($this->getBucketName($access))
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