<?php
namespace Tattler\Base\Channels;


/**
 * @skeleton
 */
interface IChannel
{
	/**
	 * @return static
	 */
	public function setName(...$channelNameArgs);
	public function getName(): string;
	public function allow(IUser $user): bool;
	public function deny(IUser $user): bool;
	public function isAllowed(IUser $user): bool;
}