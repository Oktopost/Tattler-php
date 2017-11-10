<?php
namespace Tattler;


use Skeleton\Skeleton;
use Skeleton\ConfigLoader\DirectoryConfigLoader;


class TattlerScope
{
	/** @var Skeleton */
	private static $skeleton = null;
	
	
	private static function configureSkeleton()
	{
		self::$skeleton = new Skeleton();
		
		self::$skeleton
			->enableKnot()
			->registerGlobalFor(__NAMESPACE__)
			->setConfigLoader(
				new DirectoryConfigLoader(realpath(__DIR__ . '/../../skeleton'))
			);
	}
	
	
	/**
	 * @param string|null $interface
	 * @return mixed|Skeleton
	 */
	public static function skeleton(?string $interface = null)
	{
		if (!self::$skeleton)
		{
			self::configureSkeleton();
		}
		
		if ($interface)
		{
			return self::$skeleton->get($interface);
		}
		
		return self::$skeleton;
	}
}