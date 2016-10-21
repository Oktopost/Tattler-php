<?php
namespace Tattler\Decorators\DB;


use Tattler\Objects\TattlerAccess;
use Tattler\Base\Decorators\IDBDecorator;

use Squid\Object\IObjectConnector;


/**
 * Class SquidDecorator
 */
class SquidDecorator implements IDBDecorator
{
    /** @var IObjectConnector $db */
    private $db;

    /** @var string $tableName */
    private $tableName;


    /**
     * @param TattlerAccess $access
     * @return array
     */
    private function getAccessFields(TattlerAccess $access)
    {
        return [
            'UserToken' => $access->UserToken,
            'Channel'   => $access->Channel,
        ];
    }


    /**
     * SquidConnector constructor.
     * @param IObjectConnector $connector
     * @param string           $tableName
     */
    public function __construct(IObjectConnector $connector, $tableName)
    {
        $this->db = $connector;
        $this->tableName = $tableName;
    }


    /**
     * @param TattlerAccess $access
     * @param  int          $ttl
     * @return bool
     */
    public function insertAccess(TattlerAccess $access, $ttl)
    {
        return $this->db->insert($access);
    }

    /**
     * @param TattlerAccess $access
     * @param int           $newTTL
     * @return mixed
     */
    public function updateAccessTTL(TattlerAccess $access, $newTTL)
    {
        return $this->db->updateByFields([
            'Modified' => (new \DateTime())->format('Y-m-d H:i:s')
        ], $this->getAccessFields($access));
    }

    /**
     * @param TattlerAccess $access
     * @return bool
     */
    public function accessExists(TattlerAccess $access)
    {
        return $this->db->loadOneByFields($this->getAccessFields($access)) != false;
    }

    /**
     * @param TattlerAccess $access
     * @return bool
     */
    public function deleteAccess(TattlerAccess $access)
    {
        return $this->db->deleteByFields($this->getAccessFields($access));
    }

    /**
     * @param string $userToken
     * @return bool|\Tattler\Objects\TattlerAccess[]
     */
    public function loadAllChannels($userToken)
    {
        $result = $this->db->loadAllByField('UserToken', $userToken);
        $locked = [];

        foreach($result as $key=>$value)
        {
            if ($value->IsLocked)
            {
                $locked[] = $value;
                unset($result[$key]);
            }
        }

        foreach($locked as $item)
        {
            $this->unlock($item);
        }

        return $result;
    }

    /**
     * @param int $maxTTL
     * @return bool
     */
    public function removeGarbage($maxTTL)
    {
        $date = (new \DateTime())->modify(-$maxTTL.' seconds');

        return $this->db->getConnector()
            ->delete()
            ->from($this->tableName)
            ->where('Modified <= ?', [$date->format('Y-m-d H:i:s')])
        ->executeDml();
    }

    /**
     * @param TattlerAccess $access
     * @return bool
     */
    public function lock(TattlerAccess $access)
    {
        return $this->db->updateByFields([
            'IsLocked' => 1
        ], $this->getAccessFields($access));
    }

    /**
     * @param TattlerAccess $access
     * @return bool
     */
    public function unlock(TattlerAccess $access)
    {
        return $this->db->updateByFields([
            'IsLocked' => 0
        ], $this->getAccessFields($access));
    }
}