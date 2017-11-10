<?php
namespace Tattler\Base\Modules;


use Tattler\Base\Channels\IRoom;
use Tattler\Objects\TattlerConfig;
use Tattler\Base\Channels\IChannel;
use Tattler\Base\Channels\IUser;
use Tattler\Base\Objects\ITattlerMessage;


/**
 * @skeleton
 */
interface ITattlerModule
{
    public const WS_PROTOCOL = 'ws:';
	public const WSS_PROTOCOL = 'wss:';
	public const HTTP_PROTOCOL = 'http:';
	public const HTTPS_PROTOCOL = 'https:';
	public const DEFAULT_PORT = 80;
	public const DEFAULT_SECURE_PORT = 443;


    public function setConfig(TattlerConfig $config): ITattlerModule;
    public function setConfigValue(string $key, $value): bool;
    public function getWsAddress(): string;
    public function getJWTToken(): string;
	
    public function getSavedChannels(IUser $user, bool $unlock = true): array;
    public function getDefaultChannels(IUser $user): array;
    public function getChannels(?array $filter = []): array;
    
    public function setUser(IUser $user): ITattlerModule;
	
	public function allowAccess(IRoom $room, ?IUser $user = null): bool;
	public function denyAccess(IRoom $room, ?IUser $user = null): bool;
	public function isAllowed(IRoom $room, ?IUser $user = null): bool;
    
    public function broadcast(): ITattlerModule;
    public function room(IChannel $room): ITattlerModule;
    public function user(IUser $user): ITattlerModule;

    public function message(ITattlerMessage $message): ITattlerModule;
    
    public function say(): bool;
}