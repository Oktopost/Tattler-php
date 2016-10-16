<?php
namespace Tattler;


use Tattler\Base\Decorators\IDBDecorator;
use Tattler\Base\Decorators\INetworkDecorator;

use Skeleton\Skeleton;
use Skeleton\ConfigLoader\DirectoryConfigLoader;


/**
 * Class Common
 */
class Common
{
	/** @var Skeleton */
	private static $skeleton = null;
	
	private static $database = null;
	
	private static $network = null;
	
	
	private static function configureSkeleton()
	{
		self::$skeleton = new Skeleton();
		
		self::$skeleton
			->enableKnot()
			->registerGlobalFor('Tattler')
			->setConfigLoader(
				new DirectoryConfigLoader(realpath(__DIR__ . '/../../skeleton'))
			);
	}
	
	
	/**
	 * @param string|null $interface
	 * @return mixed|Skeleton
	 */
	public static function skeleton($interface = null)
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
	
	/**
	 * @param null $decorator
	 * @return null|IDBDecorator
	 */
	public static function database($decorator = null)
	{
		if ($decorator)
		{
			if (!($decorator instanceof IDBDecorator))
			{
				throw new \Exception('DB decorator must be an instance of IDBDecorator');
			}
			
			self::$database = $decorator;
		}
		
		if (!self::$database)
		{
			
			$decorator = Common::skeleton(IDBDecorator::class);
			
			if (!$decorator)
			{
				throw new \Exception('DB decorator missing');
			}
			
			self::$database = $decorator;
		}
		
		return self::$database;
	}
	
	/**
	 * @param null $decorator
	 * @return null|INetworkDecorator
	 */
	public static function network($decorator = null)
	{
		if ($decorator)
		{
			if (!($decorator instanceof INetworkDecorator))
			{
				throw new \Exception('Network decorator must be an instance of INetworkDecorator');
			}
			
			self::$network = $decorator;
		}
		
		if (!self::$network)
		{
			$decorator = Common::skeleton(INetworkDecorator::class);
			
			if (!$decorator)
			{
				throw new \Exception('Network decorator missing');
			}
			
			self::$network = $decorator;
		}
		
		return self::$network;
	}
}