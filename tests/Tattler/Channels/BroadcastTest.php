<?php
namespace Tattler\Channels;


use Tattler\Base\Channels\IChannel;


class BroadcastTest extends \PHPUnit_Framework_TestCase
{
	/** @var IChannel $room */
	private $room;
	
	protected function setUp()
	{
		parent::setUp();
		$this->room = new Broadcast();
	}
	
	public function test_is_IChannel_instance()
    {
    	self::assertInstanceOf(Broadcast::class, $this->room);
    	self::assertInstanceOf(IChannel::class, $this->room);
    }
    
    public function test_should_return_static_on_setName()
    {
	    self::assertInstanceOf(IChannel::class, $this->room->setName(uniqId()));
    }

    public function test_should_have_broadcast_name()
    {
    	self::assertTrue($this->room->getName() === Broadcast::BROADCAST_NAME);
    }

    public function test_should_allow_to_connect_any_user()
    {
    	self::assertTrue($this->room->allow(getDummyUser()));
    }

    public function test_can_not_deny_access()
    {
	    self::assertFalse($this->room->deny(getDummyUser()));
    }

    public function test_is_always_allowed()
    {
	    self::assertTrue($this->room->isAllowed(getDummyUser()));
    }
}
