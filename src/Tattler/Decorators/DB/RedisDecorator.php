<?php
namespace Tattler\Decorators\DB;


use Tattler\Objects\TattlerAccess;
use Tattler\Base\Decorators\IDBDecorator;

use Objection\Mapper;
use Objection\LiteObject;

use Predis\Client;
use ReflectionClass;


class RedisDecorator implements IDBDecorator
{
	/** @var Client */
    private $client;

    /** @var string */
    private $prefix;
	
	
    private function getClassShortName(LiteObject $object): string 
    {
        $reflection = new ReflectionClass($object);
        return $reflection->getShortName();
    }
	
	private function getAccessObjectKey(TattlerAccess $object): string
    {
        return $this->prefix . ':' . $this->getClassShortName($object).':'.$object->UserToken;
    }
	
    
	public function __construct(string $host = 'localhost', int $port = 6379, string $prefix = 'php-tattler')
	{
		$this->client = new Client([
			'scheme' => 'tcp',
			'host'   => $host,
			'port'   => $port,
		]);
		
		$this->prefix = $prefix;
	}
	
	
	public function getConnection(): Client
	{
		return $this->client;
	}
	
    public function insertAccess(TattlerAccess $access, int $ttl): bool
    {
        $key = $this->getAccessObjectKey($access);
        $field = $access->Channel;
        $data = Mapper::getJsonFor($access);

        $result = $this->client->hset($key, $field, $data);

        if ($result) {
            $this->client->expire($key, $ttl);
            return true;
        }

        return false;
    }

    public function updateAccessTTL(TattlerAccess $access, int $newTTL): bool
    {
        $key = $this->getAccessObjectKey($access);
        return $this->client->expire($key, $newTTL);
    }

    public function accessExists(TattlerAccess $access): bool
    {
        $result = $this->client->hget($this->getAccessObjectKey($access), $access->Channel);
        return $result != null;
    }

    public function deleteAccess(TattlerAccess $access): bool
    {
        return $this->client->hdel($this->getAccessObjectKey($access), [$access->Channel]);
    }

    public function loadAllChannels(string $userToken, bool $unlock = true): array
    {
        $tmpAccess = new TattlerAccess();
        $tmpAccess->UserToken = $userToken;
        $data = $this->client->hgetall($this->getAccessObjectKey($tmpAccess));

        if (!$data)
            return [];

        /** @var TattlerAccess[] $result */
        $result = Mapper::getObjectsFrom(TattlerAccess::class, $data);
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