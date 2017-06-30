<?php
use Tattler\SkeletonInit;

use Tattler\Base\Channels\IRoom;
use Tattler\Base\Channels\IUser;

use Tattler\Base\Decorators\IDBDecorator;
use Tattler\Base\Decorators\INetworkDecorator;

use Skeleton\Type;


SkeletonInit::skeleton()->set(IDBDecorator::class, Tests\Tattler\Decorators\DB\DummyDecorator::class, Type::Singleton);
SkeletonInit::skeleton()->set(INetworkDecorator::class, Tests\Tattler\Decorators\Network\DummyDecorator::class);


/**
 * @return IUser
 */
function getDummyUser()
{
	/** @var IUser $user */
	$user = SkeletonInit::skeleton(IUser::class);
	$user->setName(uniqId(), uniqId(), uniqId());
	
	return $user;
}

/**
 * @return IRoom
 */
function getDummyRoom()
{
	/** @var IRoom $result */
	$result = SkeletonInit::skeleton(IRoom::class);
	return $result;
}