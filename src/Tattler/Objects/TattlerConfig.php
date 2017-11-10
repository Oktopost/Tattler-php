<?php
namespace Tattler\Objects;


use Tattler\Base\Decorators\IDBDecorator;
use Tattler\Base\Decorators\INetworkDecorator;

use Objection\LiteSetup;
use Objection\LiteObject;


/**
 * @property string				$WsAddress
 * @property string				$ApiAddress
 * @property string				$Namespace
 * @property string				$Secret
 * @property int				$TokenTTL
 * @property IDBDecorator		$DBDecorator
 * @property INetworkDecorator	$NetworkDecorator
 */
class TattlerConfig extends LiteObject
{
	/**
	 * @return array
	 */
	protected function _setup()
	{
		return [
			'WsAddress' 		=> LiteSetup::createString(),
			'ApiAddress'		=> LiteSetup::createString(),
			'Namespace' 		=> LiteSetup::createString(),
			'Secret'    		=> LiteSetup::createString(),
			'TokenTTL'   		=> LiteSetup::createInt(60),
			'DBDecorator'		=> LiteSetup::createInstanceOf(IDBDecorator::class),
			'NetworkDecorator'	=> LiteSetup::createInstanceOf(INetworkDecorator::class)
		];
	}
	
}