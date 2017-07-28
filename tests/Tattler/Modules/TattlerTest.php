<?php
namespace Tests\Tattler\Modules;


use Tattler\SkeletonInit;
use Tattler\Channels\Broadcast;
use Tattler\Base\Channels\IRoom;
use Tattler\Objects\TattlerConfig;
use Tattler\Base\Modules\ITattler;
use Tattler\Base\Channels\IChannel;
use Tattler\Base\Objects\ITattlerMessage;

use PHPUnit\Framework\TestCase;


class TattlerTest extends TestCase
{
	/** @var ITattler $tattler */
	private $tattler;
	
	
	private function getConfig()
	{
		$result = new TattlerConfig();
		$result->Namespace = 'Test';
		$result->WsAddress = 'ws://localhost.domain.tld';
		$result->ApiAddress = 'http://localhost.domain.tld';
		$result->Secret = uniqid();
		
		return $result;
	}
	
	/**
	 * @return ITattlerMessage
	 */
	private function getDummyMessage()
	{
		/** @var ITattlerMessage $message */
		$message = SkeletonInit::skeleton(ITattlerMessage::class);
		$message
			->setHandler('handler')
			->setNamespace('global')
			->setPayload(['some', 'random', 'data']);
		
		return $message;
	}
	
	
	protected function setUp()
	{
		parent::setUp();
		
		$this->tattler = SkeletonInit::skeleton(ITattler::class);
		$this->tattler->setConfig($this->getConfig());
	}
	
	
	public function test_setConfig_should_return_static()
	{
		self::assertInstanceOf(ITattler::class, $this->tattler->setConfig($this->getConfig()));
	}
	
	public function test_getWsAddress_should_return_WS_address()
	{
		$result = $this->tattler->getWsAddress();
		self::assertEquals('ws://localhost.domain.tld', $result);
	}
	
	
	public function test_getSavedChannels_should_return_array()
	{
		self::assertTrue(is_array($this->tattler->getSavedChannels(getDummyUser())));
	}
	
	public function test_getSavedChannel_should_return_IChannel_array()
	{
		$user = getDummyUser();
		$room = getDummyRoom();
		$room->setName(uniqId())->allow($user);
		
		$result = $this->tattler->getSavedChannels($user);
		
		self::assertNotEmpty($result);
		
		foreach ($result as $channel)
		{
			self::assertInstanceOf(IChannel::class, $channel);
		}
	}
	
	public function test_getDefaultChannels_should_return_default_channel_names()
	{
		$user = getDummyUser();
		$expected = [
			$user->getName(),
			Broadcast::BROADCAST_NAME
		];
		
		self::assertEquals($expected, $this->tattler->getDefaultChannels($user));
	}
	
	public function test_getChannels_should_return_array_of_string()
	{
		$user = getDummyUser();
		$user->setSocketId(uniqid());
		$result = $this->tattler->setUser($user)->getChannels();
		
		self::assertNotEmpty($result);
		
		foreach ($result as $value)
		{
			self::assertTrue(is_string($value));
		}
	}
	
	public function test_getChannels_with_filter_should_return_filtered_array()
	{
		$user = getDummyUser();
		$user->setSocketId(uniqid());
		
		/** @var IRoom $room */
		$room = getDummyRoom()->setName(uniqId());
		
		/** @var IRoom $room2 */
		$room2 = getDummyRoom()->setName(uniqId());
		
		$room->allow($user);
		$room2->allow($user);

		$filter = [$room->getName()];
		$result = $this->tattler->setUser($user)->getChannels($filter);

		foreach ($result as $value)
		{
			self::assertTrue($value !== $room2->getName());
		}
	}
	
	public function test_setUser_should_return_static()
	{
		self::assertInstanceOf(ITattler::class, $this->tattler->setUser(getDummyUser()));
	}
	
	public function test_set_broadcast_target_should_return_static()
	{
		self::assertInstanceOf(ITattler::class, $this->tattler->broadcast());
	}
	
	public function test_set_room_target_should_return_static()
	{
		$room = getDummyRoom();
		$room->setName(uniqId());
		self::assertInstanceOf(ITattler::class, $this->tattler->room($room));
	}
	
	public function test_set_room_target_without_name_should_throw_exception()
	{
		self::expectException(\Exception::class);
		$this->tattler->room(getDummyRoom());
	}
	
	public function test_set_user_target_should_return_static()
	{
		self::assertInstanceOf(ITattler::class, $this->tattler->user(getDummyUser()));
	}
	
	public function test_set_message_should_return_static()
	{
		$message = $this->getDummyMessage();		
		self::assertInstanceOf(ITattler::class, $this->tattler->message($message));
	}
	
	public function test_say_should_return_true()
	{
		$this->tattler->broadcast()->message($this->getDummyMessage());
		
		self::assertTrue($this->tattler->say());
	}
	
	public function test_MessageAlwaysContainsDefaultNamespace()
	{
		$message = $this->getDummyMessage();
		$message->setNamespace(null);
		$array = $message->toArray();
		self::assertSame($array['namespace'], ITattlerMessage::DEFAULT_NAMESPACE);
	}
	
	public function test_getTokenReturnString()
	{
		$result = $this->tattler->getJWTToken();
		self::assertTrue(is_string($result));
		self::assertNotEmpty($result);
	}
}
