<?php
namespace Tattler\Base\Modules;


use Tattler\Objects\TattlerConfig;
use Tattler\Base\Channels\IChannel;
use Tattler\Base\Channels\IUser;
use Tattler\Base\Objects\ITattlerMessage;


/**
 * Interface ITattler
 */
interface ITattler
{
    /**
     * @param TattlerConfig $config
     * @return static
     */
    public function setConfig(TattlerConfig $config);


    /**
     * @return string
     */
    public function getWsAddress();

    /**
     * @return string
     */
    public function getHttpAddress();

    /**
     * @param IUser $user
     * @param bool  $unlock
     * @return IChannel[]|[]
     */
    public function getSavedChannels(IUser $user, $unlock = true);

    /**
     * @param array $filter
     * @return string[]|[]
     */
    public function getChannels(array $filter = []);

    /**
     * @param IUser $user
     * @return static
     */
    public function setUser(IUser $user);

    /**
     * @return static
     */
    public function broadcast();

    /**
     * @param IChannel $room
     * @return static
     */
    public function room(IChannel $room);

    /**
     * @param IUser $user
     * @return static
     */
    public function user(IUser $user);


    /**
     * @param ITattlerMessage $message
     * @return static
     */
    public function message(ITattlerMessage $message);

    /**
     * @return bool
     */
    public function say();
}