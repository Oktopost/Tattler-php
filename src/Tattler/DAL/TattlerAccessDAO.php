<?php namespace Tattler\DAL;


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
    const DATA_TTL = 604800; // week


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


    /**
     * @param TattlerAccess $access
     * @return bool
     */
    public function allow(TattlerAccess $access)
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

    /**
     * @param TattlerAccess $access
     * @return bool
     */
    public function exists(TattlerAccess $access)
    {
        return $this->decorator->accessExists($access);
    }

    /**
     * @param TattlerAccess $access
     * @return bool
     */
    public function deny(TattlerAccess $access)
    {
        if (!$this->exists($access))
            return true;

        return $this->decorator->deleteAccess($access);
    }

    /**
     * @param      $userToken
     * @param bool $unlock
     * @return IRoom[]|[]
     */
    public function loadAllChannels($userToken, $unlock = true)
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

    /**
     * @param string $userToken
     * @param bool   $unlock
     * @return array
     */
    public function loadAllChannelNames($userToken, $unlock = true)
    {
        $result = [];

        /** @var TattlerAccess[] $query */
        $query = $this->decorator->loadAllChannels($userToken, $unlock);

        if (!$query)
            return $result;

        /** @var TattlerAccess $item */
        foreach ($query as $item)
        {
            $result[] = $item->Channel;
        }

        return $result;
    }

    /**
     * @param TattlerAccess $access
     * @return bool
     */
    public function lock(TattlerAccess $access)
    {
        return $this->decorator->lock($access);
    }

    /**
     * @return bool
     */
    public function removeOld()
    {
        return $this->decorator->removeGarbage(self::DATA_TTL);
    }
}