<?php
namespace Tattler\Base\Modules;


use Tattler\Objects\TattlerConfig;
use Tattler\Base\Channels\IChannel;
use Tattler\Base\Channels\IUser;
use Tattler\Base\Objects\ITattlerMessage;


/**
 * @skeleton
 */
interface ITattler
{
    public const WS_PROTOCOL = 'ws:';
	public const WSS_PROTOCOL = 'wss:';
	public const HTTP_PROTOCOL = 'http:';
	public const HTTPS_PROTOCOL = 'https:';
	public const DEFAULT_PORT = 80;
	public const DEFAULT_SECURE_PORT = 443;


    public function setConfig(TattlerConfig $config): ITattler;
    public function setConfigValue(string $key, string $value): bool;
    public function getWsAddress(): string;
    public function getJWTToken(): string;
	
    public function getSavedChannels(IUser $user, bool $unlock = true): array;
    public function getDefaultChannels(IUser $user): array;
    public function getChannels(?array $filter = []): array;
    
    public function setUser(IUser $user): ITattler;
    
    public function broadcast(): ITattler;
    public function room(IChannel $room): ITattler;
    public function user(IUser $user): ITattler;

    public function message(ITattlerMessage $message): ITattler;
    
    public function say(): bool;
}