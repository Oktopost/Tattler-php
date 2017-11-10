<?php
namespace Tattler\Base\Objects;


interface ITattlerMessage
{
    public const DEFAULT_NAMESPACE = 'global';


    public function setHandler(string $handler): ITattlerMessage;
	public function setNamespace(?string $namespace = null): ITattlerMessage;
	public function setPayload(array $payload): ITattlerMessage;
	public function toArray(array $filter = [], array $exclude = []): array;
}