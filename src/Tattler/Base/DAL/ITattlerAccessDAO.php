<?php
namespace Tattler\Base\DAL;

use Tattler\Base\Channels\IChannel;
use Tattler\Objects\TattlerAccess;


/**
 * @skeleton
 */
interface ITattlerAccessDAO
{
    /**
     * @param TattlerAccess $access
     * @return bool
     */
    public function allow(TattlerAccess $access);

    /**
     * @param TattlerAccess $access
     * @return bool
     */
    public function deny(TattlerAccess $access);

    /**
     * @param      $userToken
     * @param bool $unlock
     * @return IChannel[]|[]
     */
    public function loadAllChannels($userToken, $unlock = true);

    /**
     * @param      $userToken
     * @param bool $unlock
     * @return array
     */
    public function loadAllChannelNames($userToken, $unlock = true);

    /**
     * @param TattlerAccess $access
     * @return bool
     */
    public function lock(TattlerAccess $access);

    /**
     * @param TattlerAccess $access
     * @return bool
     */
    public function exists(TattlerAccess $access);

    /**
     * @return bool
     */
    public function removeOld();
}