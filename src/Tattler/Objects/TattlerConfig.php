<?php
namespace Tattler\Objects;


use Objection\LiteObject;
use Objection\LiteSetup;


/**
 * @property $Server
 * @property $Secure
 * @property $Namespace
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
            'Secure'    => LiteSetup::createBool(),
            'Namespace' => LiteSetup::createString()
        ];
    }
}