<?php
namespace Tattler\Base\DAL;


use Tattler\Base\Decorators\IDBDecorator;
use Tattler\Objects\TattlerAccess;


/**
 * @skeleton
 */
interface ITattlerAccessDAO
{
	public function setDBDecorator(IDBDecorator $dbDecorator): void;
    public function allow(TattlerAccess $access): bool;
    public function deny(TattlerAccess $access): bool;
	public function loadAllChannels(string $userToken, bool $unlock = true): array;
    public function loadAllChannelNames(string $userToken, bool $unlock = true): array;
    public function lock(TattlerAccess $access): bool;
    public function exists(TattlerAccess $access): bool;
    public function removeOld(): bool;
}