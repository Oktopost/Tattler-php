<?php
namespace Tattler\Base\Channels;


use Tattler\Channels\Room;
use Tattler\Channels\User;


$this->set(IRoom::class, Room::class);
$this->set(IUser::class, User::class);