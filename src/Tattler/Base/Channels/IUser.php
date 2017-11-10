<?php 
namespace Tattler\Base\Channels;


interface IUser extends IChannel
{
    public function setNameConverter(\Closure $callback): IUser;
    public function setSocketId($socketId): IUser;
	public function getSocketId(): string;
}