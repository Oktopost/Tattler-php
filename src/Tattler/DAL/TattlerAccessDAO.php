<?php 
namespace Tattler\DAL;


use Tattler\SkeletonInit;
use Tattler\Objects\TattlerAccess;

use Tattler\Base\Channels\IRoom;
use Tattler\Base\DAL\ITattlerAccessDAO;
use Tattler\Base\Decorators\IDBDecorator;


/**
 * Class TattlerAccessDAO
 */
class TattlerAccessDAO implements ITattlerAccessDAO
{
    private const DATA_TTL = 604800; // week


    /** @var IDBDecorator $decorator */
    private $decorator;


    /**
     * TattlerAccessDAO constructor.
     */
    public function __construct()
    {
        $this->decorator = SkeletonInit::skeleton(IDBDecorator::class);
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

        /** @var TattlerAccess $item */
        foreach ($query as $item)
        {
            /** @var IRoom $room */
            $room = SkeletonInit::skeleton(IRoom::class);
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

        foreach ($query as $item)
        {
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