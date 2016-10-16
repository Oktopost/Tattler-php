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
     * @param $userToken
     * @return TattlerAccess[]|bool
     */
    public function loadAllChannels($userToken);

    /**
     * @param int $maxTTL
     * @return bool
     */
    public function removeGarbage($maxTTL);
}