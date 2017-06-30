<?php 
namespace Tattler\Base\Channels;


use Closure;


/**
 * @skeleton
 */
interface IUser extends IChannel
{
    public function setNameConverter(Closure $callback): IUser;
    public function setSocketId($socketId): IUser;
	public function getSocketId(): ?string;
}