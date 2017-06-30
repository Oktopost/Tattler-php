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


    public function setHandler(string $handler): ITattlerMessage
    {
        $this->handler = $handler;
        return $this;
    }

    public function setNamespace(?string $namespace = null): ITattlerMessage
    {
        $this->namespace = $namespace;
        return $this;
    }

    public function setPayload(array $payload): ITattlerMessage
    {
        $this->payload = $payload;
        return $this;
    }

    public function toArray(array $filter = [], array $exclude = []): array
    {
        if (!$this->namespace)
        {
            $this->namespace = ITattlerMessage::DEFAULT_NAMESPACE;
        }

        return parent::toArray($filter, $exclude);
    }
}