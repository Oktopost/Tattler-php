<?php
namespace Tattler\Base\Decorators;


use Tattler\Objects\TattlerAccess;


/**
 * Interface IDBDecorator
 */
interface IDBDecorator
{
    /**
     * @param TattlerAccess $access
     * @param  int $ttl
     * @return bool
     */
    public function insertAccess(TattlerAccess $access, $ttl);

    /**
     * @param TattlerAccess $access
     * @param int           $newTTL
     * @return mixed
     */
    public function updateAccessTTL(TattlerAccess $access, $newTTL);

    /**
     * @param TattlerAccess $access
     * @return bool
     */
    public function accessExists(TattlerAccess $access);

    /**
     * @param TattlerAccess $access
     * @return bool
     */
    public function deleteAccess(TattlerAccess $access);

    /**
     * @param string $userToken
     * @param int $afterLockTTL
     * @return bool|TattlerAccess[]
     */
    public function loadAllChannels($userToken, $afterLockTTL);

    /**
     * @param TattlerAccess $access
     * @return bool
     */
    public function lock(TattlerAccess $access);

    /**
     * @param TattlerAccess $access
     * @return bool
     */
    public function unlock(TattlerAccess $access);

    /**
     * @param int $maxTTL
     * @return bool
     */
    public function removeGarbage($maxTTL);
}