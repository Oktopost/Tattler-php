<?php
namespace Tattler\Base\Channels;


/**
 * Interface IChannel
 */
interface IChannel
{
    /**
     * @param array $channelNameArgs
     * @return static
     */
    public function setName(...$channelNameArgs);

    /**
     * @return string
     */
    public function getName();

    /**
     * @param IUser $user
     * @return bool
     */
    public function allow(IUser $user);

    /**
     * @param IUser $user
     * @return bool
     */
    public function deny(IUser $user);
}