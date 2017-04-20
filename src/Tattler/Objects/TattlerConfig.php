<?php
namespace Tattler\Objects;


use Objection\LiteSetup;
use Objection\LiteObject;


/**
 * @property string $Server
 * @property int    $Port
 * @property bool   $Secure
 * @property string $Namespace
 * @property string $Secret
 * @property int	$TokenTTL
 */
class TattlerConfig extends LiteObject
{
    /**
     * @return array
     */
    protected function _setup()
    {
        return [
            'Server'    => LiteSetup::createString(),
            'Port'      => LiteSetup::createInt(),
            'Secure'    => LiteSetup::createBool(),
            'Namespace' => LiteSetup::createString(),
            'Secret'    => LiteSetup::createString(),
			'TokenTTL'	=> LiteSetup::createInt(60)
        ];
    }

}