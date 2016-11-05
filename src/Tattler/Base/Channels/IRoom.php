<?php namespace Tattler\Base\Channels;


/**
 * @skeleton
 */
interface IRoom extends IChannel
{
    /**
     * @param IUser $user
     * @return bool
     */
    public function lock(IUser $user);
}