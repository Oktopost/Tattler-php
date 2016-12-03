<?php


use Tattler\Common;
use Tattler\Base\Channels\IRoom;
use Tattler\Base\Channels\IUser;


require_once __DIR__ . '/../Decorators/DB/DummyDecorator.php';
require_once __DIR__ . '/../Decorators/Network/DummyDecorator.php';


Common::database(new \Tattler\Decorators\DB\DummyDecorator());
Common::network(new \Tattler\Decorators\Network\DummyDecorator());


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

function getDummyRoom()
{
	return Common::skeleton(IRoom::class);
}