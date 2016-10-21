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
        $query = $this->decorator->loadAllChannels($userToken);

        if (!$query)
            return $result;

        foreach ($query as $item)
        {
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
        $query = $this->decorator->loadAllChannels($userToken);

        if (!$query)
            return $result;

        foreach ($query as $item)
        {
            $result[] = $item->Channel;
        }

        return $result;
    }

    /**
     * @return bool
     */
    public function removeOld()
    {
        return $this->decorator->removeGarbage(self::DATA_TTL);
    }
}