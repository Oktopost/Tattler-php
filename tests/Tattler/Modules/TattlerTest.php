<?php
namespace Tests\Tattler\Modules;


use Tattler\Tattler;
use Tattler\Base\Channels\IRoom;
use Tattler\Base\Modules\ITattlerModule;
use Tattler\Base\Channels\IChannel;
use Tattler\Base\Objects\ITattlerMessage;
use Tattler\Objects\TattlerMessage;
use Tattler\Channels\Room;
use Tattler\Channels\User;
use Tattler\Channels\Broadcast;

use PHPUnit\Framework\TestCase;
use Tattler\TattlerScope;


class TattlerTest extends TestCase
{
	/** @var ITattlerModule $tattler */
	private $tattler;
	private $instanceName;
	
	
	/**
	 * @return ITattlerMessage
	 */
	private function getDummyMessage()
	{
		/** @var ITattlerMessage $message */
		$message = new TattlerMessage();
		$message
			->setHandler('handler')
			->setNamespace('global')
			->setPayload(['some', 'random', 'data']);
		
		return $message;
	}
	
	
	protected function setUp()
	{
		parent::setUp();
		
		$this->instanceName = uniqid();
		$this->tattler = Tattler::getInstance(getConfig(), $this->instanceName);
	}
	
	
	public function test_setConfig_should_return_static()
	{
		self::assertInstanceOf(ITattlerModule::class, $this->tattler->setConfig(getConfig()));
	}
	
	public function test_loader_should_store_configuration()
	{
		self::assertInstanceOf(ITattlerModule::class, Tattler::load($this->instanceName));
	}
	
	public function test_loader_should_return_null_for_unknown()
	{
		self::assertNull(Tattler::load(uniqid()));
	}
	
	public function test_tattlerScope_contains_skeleton()
	{
		self::assertInstanceOf(\Skeleton\Skeleton::class, TattlerScope::skeleton());
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
		$room->setName(uniqId());
		
		$this->tattler->allowAccess($room, $user);
		
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
		
		$this->tattler->allowAccess($room, $user);
		$this->tattler->allowAccess($room2, $user);
		
		$filter = [$room->getName()];
		$result = $this->tattler->setUser($user)->getChannels($filter);

		foreach ($result as $value)
		{
			self::assertTrue($value !== $room2->getName());
		}
	}
	
	public function test_allow_access_takes_current_user_by_default()
	{
		$user = getDummyUser();
		$user->setSocketId(uniqid());
		$this->tattler->setUser($user);
		$room = new Room();
		$room->setName(uniqid());
		
		self::assertTrue($this->tattler->allowAccess($room));
	}
	
	public function test_deny_access_takes_current_user_by_default()
	{
		$user = getDummyUser();
		$user->setSocketId(uniqid());
		$this->tattler->setUser($user);
		$room = new Room();
		$room->setName(uniqid());
		$this->tattler->allowAccess($room);
		
		self::assertTrue($this->tattler->isAllowed($room, $user));
		self::assertTrue($this->tattler->denyAccess($room));
		self::assertFalse($this->tattler->isAllowed($room, $user));
	}
	
	public function test_isAllowed_takes_current_user_by_default()
	{
		$user = getDummyUser();
		$user->setSocketId(uniqid());
		$this->tattler->setUser($user);
		$room = new Room();
		$room->setName(uniqid());
		
		$this->tattler->allowAccess($room);
		self::assertTrue($this->tattler->isAllowed($room));
		
		$this->tattler->setUser((new User())->setName(uniqid()));
		
		self::assertFalse($this->tattler->isAllowed($room));
	}
	
	public function test_setUser_should_return_static()
	{
		self::assertInstanceOf(ITattlerModule::class, $this->tattler->setUser(getDummyUser()));
	}
	
	public function test_set_broadcast_target_should_return_static()
	{
		self::assertInstanceOf(ITattlerModule::class, $this->tattler->broadcast());
	}
	
	public function test_set_room_target_should_return_static()
	{
		$room = getDummyRoom();
		$room->setName(uniqId());
		self::assertInstanceOf(ITattlerModule::class, $this->tattler->room($room));
	}
	
	public function test_set_room_target_without_name_should_throw_exception()
	{
		self::expectException(\Exception::class);
		$this->tattler->room(getDummyRoom());
	}
	
	public function test_set_user_target_should_return_static()
	{
		self::assertInstanceOf(ITattlerModule::class, $this->tattler->user(getDummyUser()));
	}
	
	public function test_set_message_should_return_static()
	{
		$message = $this->getDummyMessage();		
		self::assertInstanceOf(ITattlerModule::class, $this->tattler->message($message));
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
	
	public function test_SetConfigValueReturnTrue()
	{
		self::assertTrue($this->tattler->setConfigValue('Namespace', 'Test2'));
	}
	
	public function test_SetConfigValueReturnFalse()
	{
		self::assertFalse($this->tattler->setConfigValue('fake', 'Test'));
	}
}
