<?php
namespace Tests\Tattler\Channels;


use PHPUnit\Framework\TestCase;

use Tattler\Base\Channels\IRoom;
use Tattler\Base\Channels\IChannel;
use Tattler\Base\Modules\ITattlerModule;
use Tattler\Tattler;


class RoomTest extends TestCase
{
    private $roomName;
    
    /** @var IRoom $room */
	private $room;
	
	/** @var ITattlerModule */
	private $tattler;
	
	
	protected function setUp()
    {
	    parent::setUp();
	 
	    $this->tattler = Tattler::getInstance(getConfig());
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
		$this->room->setName($this->roomName);
		self::assertTrue($this->tattler->allowAccess($this->room, getDummyUser()));
	}
	
	public function test_allow_twice_should_return_true()
	{
		$room = $this->room->setName($this->roomName);
		$user = getDummyUser();
		$user->setSocketId(uniqid());
		
		$this->tattler->allowAccess($room, $user);
		
		self::assertTrue($this->tattler->allowAccess($room, $user));
	}
    
    public function test_deny_should_return_true()
    {
    	$user = getDummyUser();
    	$this->room->setName($this->roomName);
    	$this->tattler->allowAccess($this->room, $user);
    	self::assertTrue($this->tattler->denyAccess($this->room, $user));
    }
    
    public function test_is_allowed_for_unknown_user_should_return_false()
    {
		$this->room->setName(uniqId());
    	self::assertFalse($this->tattler->isAllowed($this->room, getDummyUser()));
    }
    
    public function test_for_denying_unknown_user_should_return_true()
    {
		$this->room->setName(uniqId());
	    self::assertTrue($this->tattler->denyAccess($this->room, getDummyUser()));
    }

    public function test_should_return_isAllowed_for_current_user()
    {
    	$user = getDummyUser();
	    $this->room->setName($this->roomName);
	    $this->tattler->allowAccess($this->room, $user);
        self::assertTrue($this->tattler->isAllowed($this->room, $user));
    }
}
