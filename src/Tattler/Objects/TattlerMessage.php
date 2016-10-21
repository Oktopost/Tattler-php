<?php
namespace Tattler\Objects;

use Objection\LiteObject;
use Objection\LiteSetup;

use Tattler\Base\Objects\ITattlerMessage;


/**
 * @property string $handler
 * @property string $namespace
 * @property array  $payload
 */
class TattlerMessage extends LiteObject implements ITattlerMessage
{
    /**
     * @return array
     */
    protected function _setup()
    {
        return [
            'handler'   => LiteSetup::createString(),
            'namespace' => LiteSetup::createString(),
            'payload'   => LiteSetup::createArray(),
        ];
    }


    /**
     * @param $handler
     * @return static
     */
    public function setHandler($handler)
    {
        $this->handler = $handler;
        return $this;
    }

    /**
     * @param null $namespace
     * @return static
     */
    public function setNamespace($namespace = null)
    {
        $this->namespace = $namespace;
        return $this;
    }

    /**
     * @param array $payload
     * @return static
     */
    public function setPayload(array $payload)
    {
        $this->payload = $payload;
        return $this;
    }

    /**
     * @param array $filter
     * @param array $exclude
     * @return array
     */
    public function toArray(array $filter = [], array $exclude = [])
    {
        if ($this->namespace == null)
        {
            $this->namespace = ITattlerMessage::DEFAULT_NAMESPACE;
        }

        return parent::toArray($filter, $exclude);
    }
}