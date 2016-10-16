<?php namespace Tattler\Base\Channels;


/**
 * Interface IRoom
 */
interface IRoom extends IChannel
{
    /**
     * @param IUser $user
     * @return bool
     */
    public function isAllowed(IUser $user);
}