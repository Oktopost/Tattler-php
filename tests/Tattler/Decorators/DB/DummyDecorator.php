<?php
namespace Tests\Tattler\Decorators\DB;


use Tattler\Objects\TattlerAccess;
use Tattler\Base\Decorators\IDBDecorator;


class DummyDecorator implements IDBDecorator
{

    private static $accessStorage = [];

    
    public function insertAccess(TattlerAccess $access, int $ttl): bool
    {
        self::$accessStorage[] = $access;
        return true;
    }

    public function updateAccessTTL(TattlerAccess $access, int $newTTL): bool
    {
        return true;
    }

    public function accessExists(TattlerAccess $access): bool
    {
        /** @var TattlerAccess $item */
        foreach (self::$accessStorage as $item)
        {
            if ($item->Channel == $access->Channel && $item->UserToken == $access->UserToken)
            {
                return true;
            }
        }

        return false;
    }

    public function deleteAccess(TattlerAccess $access): bool
    {
        /** @var TattlerAccess $item */
        foreach (self::$accessStorage as $key => $item)
        {
            if ($item->Channel == $access->Channel && $item->UserToken == $access->UserToken)
            {
                unset(self::$accessStorage[$key]);
                return true;
            }
        }

        return false;
    }

    public function loadAllChannels(string $userToken, bool $unlock = true): array
    {
        $result = [];

        /** @var TattlerAccess $item */
        foreach (self::$accessStorage as $item)
        {
            if ($item->UserToken == $userToken)
            {
                $result[] = $item;
            }
        }

        return $result;
    }

    public function lock(TattlerAccess $access): bool
    {
    	/** @var TattlerAccess $item */
	    foreach(self::$accessStorage as $item)
	    {
		    if ($item->Channel == $access->Channel && $item->UserToken == $access->UserToken)
		    {
		    	$item->IsLocked = true;
		    	return true;
	        }
	    }
        
	    return false;
    }

    public function unlock(TattlerAccess $access): bool
    {
	    /** @var TattlerAccess $item */
	    foreach(self::$accessStorage as $item)
	    {
		    if ($item->Channel == $access->Channel && $item->UserToken == $access->UserToken)
		    {
			    $item->IsLocked = false;
			    return true;
		    }
	    }
	
	    return false;
    }

    public function removeGarbage(int $maxTTL): bool
    {
        return true;
    }
}