<?php
namespace Tattler;


use Tattler\Base\Modules\ITattlerModule;

use Tattler\Objects\TattlerConfig;


class Tattler
{
	private static $configurations = [];
	
	
	public static function getInstance(TattlerConfig $config, ?string $instanceName = null): ITattlerModule
	{
		if (!$instanceName)
		{
			$instanceName = uniqid();
		}
		
		if (!isset(self::$configurations[$instanceName]))
		{
			self::$configurations[$instanceName] = $config;
		}
		
		/** @var ITattlerModule $result */
		$result = TattlerScope::skeleton(ITattlerModule::class);
		$result->setConfig(self::$configurations[$instanceName]);
		
		return $result;
	}
	
	public static function load(string $instanceName): ?ITattlerModule
	{
		if (!isset(self::$configurations[$instanceName]))
		{
			return null;
		}
		
		/** @var ITattlerModule $result */
		$result = TattlerScope::skeleton(ITattlerModule::class);
		$result->setConfig(self::$configurations[$instanceName]);
		
		return $result;
	}
}