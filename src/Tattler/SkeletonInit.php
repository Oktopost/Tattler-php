<?php
namespace Tattler;


use Skeleton\Skeleton;
use Skeleton\Base\ISkeletonInit;
use Skeleton\ConfigLoader\DirectoryConfigLoader;


class SkeletonInit implements ISkeletonInit
{
	private const CONFIG_PATH = __DIR__ . '/../../skeleton';
	
	
	/** @var Skeleton */
	private static $skeleton = null;
	
	
	private static function configureSkeleton()
	{
		self::$skeleton = new Skeleton();
		
		self::$skeleton
			->enableKnot()
			->registerGlobalFor(__NAMESPACE__)
			->setConfigLoader(
				new DirectoryConfigLoader(realpath(self::CONFIG_PATH))
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