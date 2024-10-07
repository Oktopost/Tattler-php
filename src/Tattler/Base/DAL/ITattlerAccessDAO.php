<?php
namespace Tattler\Base\DAL;


use Tattler\Base\Connectors\IDBConnector;
use Tattler\Objects\TattlerAccess;


/**
 * @skeleton
 */
interface ITattlerAccessDAO
{
	public function setDBConnector(IDBConnector $dbConnector): void;
    public function allow(TattlerAccess $access): bool;
    public function deny(TattlerAccess $access): bool;
	public function loadAllChannels(string $userToken, bool $unlock = true): array;
    public function loadAllChannelNames(string $userToken, bool $unlock = true): array;
    
    /** @deprecated  */
    public function lock(TattlerAccess $access): bool;
    public function exists(TattlerAccess $access): bool;
    public function removeOld(): bool;
}