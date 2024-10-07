<?php
namespace Tattler\Base\Connectors;


use Tattler\Objects\TattlerAccess;


/**
 * @skeleton
 */
interface IDBConnector
{
    public function insertAccess(TattlerAccess $access, int $ttl): bool;
    public function updateAccessTTL(TattlerAccess $access, int $newTTL): bool;
    public function accessExists(TattlerAccess $access): bool;
    public function deleteAccess(TattlerAccess $access): bool;
	public function loadAllChannels(string $userToken, bool $unlock = true): array;
	public function lock(TattlerAccess $access): bool;
    public function unlock(TattlerAccess $access): bool;
    public function removeGarbage(int $maxTTL): bool;
}