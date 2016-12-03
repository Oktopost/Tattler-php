<?php
namespace Tests\Tattler\Channels;


use Tattler\Base\Channels\IRoom;
use Tattler\Base\Channels\IChannel;

use PHPUnit_Framework_TestCase;


require_once __DIR__ .'/../config.php';


class RoomTest extends PHPUnit_Framework_TestCase
{
    private $roomName;
    
    /** @var IRoom $room */
	private $room;
	
	protected function setUp()
    {
	    parent::setUp();
	    
	    $this->room = getDummyRoom();
	    $this->roomName = uniqid();
    }
	
	public function test_is_IRoom_instance()
	{
		self::assertInstanceOf(IRoom::class, $this->room);
		self::assertInstanceOf(IChannel::class, $this->room);
	}
    

    public function test_should_return_static_on_setName()
    {
	    self::assertInstanceOf(IRoom::class, $this->room->setName($this->roomName));
    }
    
    public function test_should_return_valid_name()
    {
    	$this->room->setName($this->roomName);
    	self::assertEquals($this->roomName, $this->room->getName());
    }

    public function test_getName_without_name_should_throw_exception()
    {
    	self::expectException(\Exception::class);
	    $this->room->getName();
    }
	
	public function test_should_allow_access_for_specified_user()
	{
		self::assertTrue($this->room->setName($this->roomName)->allow(getDummyUser()));
	}
	
	public function test_allow_twice_should_return_true()
	{
		$room = $this->room->setName($this->roomName);
		$user = getDummyUser();
		$user->setSocketId(uniqid());
		$room->allow($user);
		$room->lock($user);
		
		self::assertTrue($room->allow($user));
	}
    
    public function test_deny_should_return_true()
    {
    	$user = getDummyUser();
    	$this->room->setName($this->roomName)->allow($user);
    	self::assertTrue($this->room->deny($user));
    }
    
    public function test_is_allowed_for_unknown_user_should_return_false()
    {
    	self::assertFalse($this->room->setName(uniqId())->isAllowed(getDummyUser()));
    }
    
    public function test_for_denying_unknown_user_should_return_true()
    {
	    self::assertTrue($this->room->setName(uniqId())->deny(getDummyUser()));
    }

    public function test_should_return_isAllowed_for_current_user()
    {
    	$user = getDummyUser();
	    $this->room->setName($this->roomName)->allow($user);
        self::assertTrue($this->room->isAllowed($user));
    }
    
    public function test_lock_should_return_true()
    {
    	$room = $this->room->setName($this->roomName);
    	$user = getDummyUser();
    	$room->allow($user);
    	self::assertTrue($room->lock($user));
    }
}
