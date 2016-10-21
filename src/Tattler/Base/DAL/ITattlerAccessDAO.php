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
     * @param $userToken
     * @return IChannel[]
     */
    public function loadAllChannels($userToken);

    /**
     * @param $userToken
     * @return array
     */
    public function loadAllChannelNames($userToken);

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