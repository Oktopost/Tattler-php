<?php
namespace Tattler\Decorators\DB;


use Tattler\Objects\TattlerAccess;
use Tattler\Base\Decorators\IDBDecorator;

use Objection\Mapper;
use Objection\LiteObject;

use Predis\Client;
use ReflectionClass;


/**
 * Class RedisDecorator
 */
class RedisDecorator implements IDBDecorator
{
    private $client;

    private $prefix;

    
    /**
     * Redis constructor.
     * @param string $host
     * @param int    $port
     * @param string $prefix
     */
    public function __construct($host = 'localhost', $port = 6379, $prefix = 'php-tattler')
    {
        $this->client = new Client([
            'scheme' => 'tcp',
            'host'   => $host,
            'port'   => $port,
        ]);

        $this->prefix = $prefix;
    }

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
     * @return Client
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
        return $this->prefix . ':' . $this->getClassShortName($object).':'.$object->UserToken;
    }

    /**
     * @param TattlerAccess $access
     * @param  int          $ttl
     * @return bool
     */
    public function insertAccess(TattlerAccess $access, $ttl)
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

    /**
     * @param TattlerAccess $access
     * @param int           $newTTL
     * @return mixed
     */
    public function updateAccessTTL(TattlerAccess $access, $newTTL)
    {
        $key = $this->getAccessObjectKey($access);
        return $this->client->expire($key, $newTTL);
    }

    /**
     * @param TattlerAccess $access
     * @return bool
     */
    public function accessExists(TattlerAccess $access)
    {
        $result = $this->client->hget($this->getAccessObjectKey($access), $access->Channel);
        return $result != null;
    }

    /**
     * @param TattlerAccess $access
     * @return bool
     */
    public function deleteAccess(TattlerAccess $access)
    {
        return $this->client->hdel($this->getAccessObjectKey($access), [$access->Channel]);
    }

    /**
     * @param string $userToken
     * @param bool   $unlock
     * @return TattlerAccess[]|bool
     */
    public function loadAllChannels($userToken, $unlock = true)
    {
        $tmpAccess = new TattlerAccess();
        $tmpAccess->UserToken = $userToken;
        $data = $this->client->hgetall($this->getAccessObjectKey($tmpAccess));

        if (!$data)
            return false;

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