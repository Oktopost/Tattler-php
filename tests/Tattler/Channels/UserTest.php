<?php namespace Tests\Tattler\Channels;


use PHPUnit\Framework\TestCase;

use Tattler\Base\Channels\IUser;
use Tattler\Base\Channels\IChannel;
use Tattler\Channels\User;


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
		$converter = function()
		{
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
	
	public function test_user_with_socketId_always_have_name()
	{
		$user = new User();
		$user->setSocketId(uniqid());
		
		self::assertNotEmpty($user->getName());
	}
	
	public function test_user_without_name_throws_exception()
	{
		$user = new User();
		
		self::expectException(\Exception::class);
		$user->getName();		
	}
}
