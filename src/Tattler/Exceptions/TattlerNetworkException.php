<?php
namespace Tattler\Exceptions;


use Throwable;


class TattlerNetworkException extends \Exception
{
	public function __construct($message = "", $code = 0, Throwable $previous = null)
	{
		parent::__construct('Tattler network exception: ' . $message, $code, $previous);
	}
}