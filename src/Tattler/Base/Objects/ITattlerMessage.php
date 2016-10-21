<?php
namespace Tattler\Base\Objects;


/**
 * Interface ITattlerMessage
 */
interface ITattlerMessage
{
    const DEFAULT_NAMESPACE = 'global';


    /**
     * @param $handler
     * @return static
     */
    public function setHandler($handler);

    /**
     * @param null $namespace
     * @return static
     */
    public function setNamespace($namespace = null);

    /**
     * @param array $payload
     * @return static
     */
    public function setPayload(array $payload);

    /**
     * @return array
     */
    public function toArray();
}