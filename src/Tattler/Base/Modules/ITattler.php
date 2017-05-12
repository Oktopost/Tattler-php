<?php
namespace Tattler\Base\Modules;


use Tattler\Objects\TattlerConfig;
use Tattler\Base\Channels\IChannel;
use Tattler\Base\Channels\IUser;
use Tattler\Base\Objects\ITattlerMessage;


/**
 * @skeleton
 */
interface ITattler
{
    const WS_PROTOCOL = 'ws:';

    const WSS_PROTOCOL = 'wss:';

    const HTTP_PROTOCOL = 'http:';

    const HTTPS_PROTOCOL = 'https:';

    const DEFAULT_PORT = 80;

    const DEFAULT_SECURE_PORT = 443;


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
    public function getJWTToken();

    /**
     * @param IUser $user
     * @param bool  $unlock
     * @return IChannel[]|[]
     */
    public function getSavedChannels(IUser $user, $unlock = true);

    /**
     * @param IUser $user
     * @return string[]
     */
    public function getDefaultChannels(IUser $user);

    /**
     * @param array $filter
     * @return string[]|[]
     */
    public function getChannels($filter = []);

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