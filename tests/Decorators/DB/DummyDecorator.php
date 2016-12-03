<?php
namespace Tests\Tattler\Decorators\DB;


use Tattler\Objects\TattlerAccess;
use Tattler\Base\Decorators\IDBDecorator;


class DummyDecorator implements IDBDecorator
{

    private $accessStorage = [];

    
    /**
     * @param TattlerAccess $access
     * @param  int          $ttl
     * @return bool
     */
    public function insertAccess(TattlerAccess $access, $ttl)
    {
        $this->accessStorage[] = $access;
        return true;
    }

    /**
     * @param TattlerAccess $access
     * @param int           $newTTL
     * @return mixed
     */
    public function updateAccessTTL(TattlerAccess $access, $newTTL)
    {
        return true;
    }

    /**
     * @param TattlerAccess $access
     * @return bool
     */
    public function accessExists(TattlerAccess $access)
    {
        /** @var TattlerAccess $item */
        foreach ($this->accessStorage as $item)
        {
            if ($item->Channel == $access->Channel && $item->UserToken == $access->UserToken)
            {
                return true;
            }
        }

        return false;
    }

    /**
     * @param TattlerAccess $access
     * @return bool
     */
    public function deleteAccess(TattlerAccess $access)
    {
        /** @var TattlerAccess $item */
        foreach ($this->accessStorage as $key => $item)
        {
            if ($item->Channel == $access->Channel && $item->UserToken == $access->UserToken)
            {
                unset($this->accessStorage[$key]);
                return true;
            }
        }

        return false;
    }

    /**
     * @param string $userToken
     * @param bool   $unlock
     * @return bool|TattlerAccess[]
     */
    public function loadAllChannels($userToken, $unlock = true)
    {
        $result = [];

        /** @var TattlerAccess $item */
        foreach ($this->accessStorage as $item)
        {
            if ($item->UserToken == $userToken)
            {
                $result[] = $item;
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
    	/** @var TattlerAccess $item */
	    foreach($this->accessStorage as $item)
	    {
		    if ($item->Channel == $access->Channel && $item->UserToken == $access->UserToken)
		    {
		    	$item->IsLocked = true;
		    	return true;
	        }
	    }
        
	    return false;
    }

    /**
     * @param TattlerAccess $access
     * @return bool
     */
    public function unlock(TattlerAccess $access)
    {
	    /** @var TattlerAccess $item */
	    foreach($this->accessStorage as $item)
	    {
		    if ($item->Channel == $access->Channel && $item->UserToken == $access->UserToken)
		    {
			    $item->IsLocked = false;
			    return true;
		    }
	    }
	
	    return false;
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