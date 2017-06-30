<?php namespace Tests\Tattler\Channels;


use PHPUnit\Framework\TestCase;

use Tattler\Base\Channels\IUser;
use Tattler\Base\Channels\IChannel;


class UserTest extends TestCase
{
	/** @var IUser $user */
	private $user;
	
	private $userName;
	
	
	private function setDummyNameConverter()
	{
		$converter = function(...$data){
			return implode(':', $data);
		};
		
		$this->user->setNameConverter($converter);
	}
	
	
	protected function setUp()
	{
		parent::setUp();
		
		$this->userName = 'Simon Petrikov';
		$this->user = getDummyUser();
		$this->setDummyNameConverter();
		$this->user->setName($this->userName);
	}
	
	public function test_is_IUser_instance()
	{
		self::assertInstanceOf(IUser::class, $this->user);
		self::assertInstanceOf(IChannel::class, $this->user);
	}
	
	public function test_setNameConverter_should_return_static()
	{
		$converter = function(...$data){
			return "i'm just dummy closure";
		};
		
		self::assertInstanceOf(IUser::class, $this->user->setNameConverter($converter));
	}

	
	public function test_should_return_static_on_setName()
	{
		self::assertInstanceOf(IUser::class, $this->user->setName(uniqid(), uniqid()));
	}
	
	public function test_should_return_valid_name()
	{
		self::assertEquals($this->userName, $this->user->getName());
	}
	
	public function test_user_can_get_his_private_room_return_true()
	{
		self::assertTrue($this->user->allow($this->user));
	}
	
	public function test_user_can_not_deny_access_to_his_private_room()
	{
		self::assertFalse($this->user->deny($this->user));
	}
	
	public function test_user_always_allowed_to_his_private_room()
	{
		self::assertTrue($this->user->isAllowed($this->user));
	}
	
	public function test_setSocketId_should_return_static()
	{
		self::assertInstanceOf(IUser::class, $this->user->setSocketId(uniqId()));
	}
	
	public function test_getSocketId_should_return_socketId()
	{
		$socketId = uniqid();
		$this->user->setSocketId($socketId);
		self::assertEquals($socketId, $this->user->getSocketId());
	}
	
	public function test_getSocketId_without_socketId_should_throw_exception()
	{
		self::expectException(\Exception::class);
		$this->user->getSocketId();
	}
}
