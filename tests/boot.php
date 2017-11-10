<?php
use Tattler\Channels\Room;
use Tattler\Channels\User;

use Tattler\Base\Channels\IRoom;
use Tattler\Base\Channels\IUser;

use Tattler\Objects\TattlerConfig;


require_once __DIR__ . '/../vendor/autoload.php';


function getConfig(): TattlerConfig
{
	$result = new TattlerConfig();
	$result->Namespace = 'Test';
	$result->WsAddress = 'ws://localhost.domain.tld';
	$result->ApiAddress = 'http://localhost.domain.tld';
	$result->Secret = uniqid();
	$result->DBDecorator = new \Tests\Tattler\Decorators\DB\DummyDecorator();
	$result->NetworkDecorator = new Tests\Tattler\Decorators\Network\DummyDecorator();
	
	return $result;
}

/**
 * @return IUser
 */
function getDummyUser()
{
	/** @var IUser $user */
	$user = new User();
	$user->setName(uniqId(), uniqId(), uniqId());
	
	return $user;
}

/**
 * @return IRoom
 */
function getDummyRoom()
{
	/** @var IRoom $result */
	$result = new Room();
	return $result;
}