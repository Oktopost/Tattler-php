<?php
namespace Tests\Tattler\Channels;


use PHPUnit\Framework\TestCase;
use Tattler\Channels\Broadcast;
use Tattler\Base\Channels\IChannel;


class BroadcastTest extends TestCase
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
    
    public function test_should_not_change_name()
	{
		$this->room->setName(uniqid());
		self::assertSame(Broadcast::BROADCAST_NAME, $this->room->getName());
	}
}
