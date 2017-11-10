<?php
namespace Tattler\Base\Channels;


interface IChannel
{
	/**
	 * @return static
	 */
	public function setName(...$channelNameArgs);
	public function getName(): string;
}