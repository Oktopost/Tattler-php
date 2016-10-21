<?php namespace Tattler\Base\Channels;


use Closure;


/**
 * Interface IUser
 */
interface IUser extends IChannel
{
    /**
     * @param Closure $callback
     * @return static
     */
    public function setNameConverter(Closure $callback);

    /**
     * @param string $socketId
     * @return static
     */
    public function setSocketId($socketId);

    /**
     * @return string|null
     */
    public function getSocketId();
}