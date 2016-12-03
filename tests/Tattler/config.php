<?php


use Tattler\Common;
use Tattler\Base\Channels\IRoom;
use Tattler\Base\Channels\IUser;


require_once __DIR__ . '/../../vendor/autoload.php';


Common::database(new \Tests\Tattler\Decorators\DB\DummyDecorator());
Common::network(new \Tests\Tattler\Decorators\Network\DummyDecorator());


/**
 * @return IUser
 */
function getDummyUser()
{
	/** @var IUser $user */
	$user = Common::skeleton(IUser::class);
	$user->setName(uniqId(), uniqId(), uniqId());
	
	return $user;
}

/**
 * @return IRoom
 */
function getDummyRoom()
{
	/** @var IRoom $result */
	$result = Common::skeleton(IRoom::class);
	return $result;
}