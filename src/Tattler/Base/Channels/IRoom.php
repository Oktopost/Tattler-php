<?php 
namespace Tattler\Base\Channels;


/**
 * @skeleton
 */
interface IRoom extends IChannel
{
    public function lock(IUser $user): bool;
}