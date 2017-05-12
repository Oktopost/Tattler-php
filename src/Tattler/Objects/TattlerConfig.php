<?php
namespace Tattler\Objects;


use Objection\LiteObject;
use Objection\LiteSetup;


/**
 * @property string $WsAddress
 * @property string $ApiAddress
 * @property string $Namespace
 * @property string $Secret
 * @property int $TokenTTL
 */
class TattlerConfig extends LiteObject
{
	/**
	 * @return array
	 */
	protected function _setup()
	{
		return [
			'WsAddress'  => LiteSetup::createString(),
			'ApiAddress' => LiteSetup::createString(),
			'Namespace'  => LiteSetup::createString(),
			'Secret'     => LiteSetup::createString(),
			'TokenTTL'   => LiteSetup::createInt(60)
		];
	}
	
}