<?php
namespace Tattler\Decorators\DB;


use Tattler\Base\Decorators\IDBDecorator;
use Tattler\Objects\TattlerAccess;

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
     * @param bool   $unlock
     * @return TattlerAccess[]|bool|
     */
    public function loadAllChannels($userToken, $unlock = true)
    {
    	/** @var TattlerAccess[] $result */
        $result = $this->db->loadAllByFields([
            'UserToken' => $userToken,
            'IsLocked' => 0
        ]);

        if ($unlock) {
            $this->db->updateByFields([
                'IsLocked' => 0
            ], [
                'UserToken' => $userToken,
                'IsLocked' => 1
            ]);
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