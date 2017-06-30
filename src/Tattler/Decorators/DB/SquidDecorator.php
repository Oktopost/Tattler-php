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
    /** @var IObjectConnector */
    private $db;

    /** @var string */
    private $tableName;


    private function getAccessFields(TattlerAccess $access): array
    {
        return [
            'UserToken' => $access->UserToken,
            'Channel'   => $access->Channel,
        ];
    }


    public function __construct(IObjectConnector $connector, string $tableName)
    {
        $this->db = $connector;
        $this->tableName = $tableName;
    }


    public function insertAccess(TattlerAccess $access, int $ttl): bool
    {
        return $this->db->insert($access);
    }

    public function updateAccessTTL(TattlerAccess $access, int $newTTL): bool
    {
        return $this->db->updateByFields([
            'Modified' => (new \DateTime())->format('Y-m-d H:i:s')
        ], $this->getAccessFields($access));
    }

    public function accessExists(TattlerAccess $access): bool
    {
        return $this->db->loadOneByFields($this->getAccessFields($access)) != false;
    }

    public function deleteAccess(TattlerAccess $access): bool
    {
        return $this->db->deleteByFields($this->getAccessFields($access));
    }

    public function loadAllChannels(string $userToken, bool $unlock = true): array
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

    public function removeGarbage(int $maxTTL): bool
    {
        $date = (new \DateTime())->modify(-$maxTTL.' seconds');

        return $this->db->getConnector()
            ->delete()
            ->from($this->tableName)
            ->where('Modified <= ?', [$date->format('Y-m-d H:i:s')])
        ->executeDml();
    }

    public function lock(TattlerAccess $access): bool
    {
        return $this->db->updateByFields([
            'IsLocked' => 1
        ], $this->getAccessFields($access));
    }

    public function unlock(TattlerAccess $access): bool
    {
        return $this->db->updateByFields([
            'IsLocked' => 0
        ], $this->getAccessFields($access));
    }
}