<?php namespace Tattler\DAL;


use Tattler\Common;
use Tattler\Base\Channels\IRoom;
use Tattler\Base\DAL\ITattlerAccessDAO;
use Tattler\Base\Decorators\IDBDecorator;
use Tattler\Objects\TattlerAccess;


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
        $this->decorator = Common::database();
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
     * @param $userToken
     * @return IRoom[]|[]
     */
    public function loadAllChannels($userToken)
    {
        $result = [];

        /** @var TattlerAccess[] $query */
        $query = $this->decorator->loadAllChannels($userToken, self::DATA_TTL);

        if (!$query)
            return $result;

        /** @var TattlerAccess $item */
        foreach ($query as $item)
        {
            if ($item->IsLocked)
                continue;

            /** @var IRoom $room */
            $room = Common::skeleton(IRoom::class);
            $room->setName($item->Channel);
            $result[] = $room;
        }

        return $result;
    }

    /**
     * @param string $userToken
     * @return array
     */
    public function loadAllChannelNames($userToken)
    {
        $result = [];

        /** @var TattlerAccess[] $query */
        $query = $this->decorator->loadAllChannels($userToken, self::DATA_TTL);

        if (!$query)
            return $result;

        /** @var TattlerAccess $item */
        foreach ($query as $item)
        {
            if ($item->IsLocked)
                continue;

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