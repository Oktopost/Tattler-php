<?php namespace Tattler\Objects;


use Objection\LiteObject;
use Objection\LiteSetup;

/**
 * @property string $Created
 * @property string $Modified
 * @property string $UserToken
 * @property string $Channel
 */
class TattlerAccess extends LiteObject
{
    /**
     * @return array
     */
    protected function _setup()
    {
        return [
            'Created'   => LiteSetup::createString(),
            'Modified'  => LiteSetup::createString(),
            'UserToken' => LiteSetup::createString(),
            'Channel'   => LiteSetup::createString()
        ];
    }


    /**
     * TattlerAccess constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $now = (new \DateTime())->format('Y-m-d H:i:s');;
        $this->Created = $now;
        $this->Modified = $now;
    }
}