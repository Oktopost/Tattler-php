<?php
namespace Tattler\Objects;


use Tattler\Base\Connectors\IDBConnector;
use Tattler\Base\Connectors\INetworkConnector;

use Objection\LiteSetup;
use Objection\LiteObject;


/**
 * @property string				$WsAddress
 * @property string				$ApiAddress
 * @property string				$Namespace
 * @property string				$Secret
 * @property int				$TokenTTL
 * @property int				$Timeout
 * @property IDBConnector		$DBConnector
 * @property INetworkConnector	$NetworkConnector
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
			'Timeout'   		=> LiteSetup::createInt(5),
			'DBConnector'		=> LiteSetup::createInstanceOf(IDBConnector::class),
			'NetworkConnector'	=> LiteSetup::createInstanceOf(INetworkConnector::class)
		];
	}
	
}