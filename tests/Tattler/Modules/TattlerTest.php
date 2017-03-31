<?php namespace Tests\Tattler\Modules;


use Tattler\Common;
use Tattler\Channels\Broadcast;
use Tattler\Base\Channels\IRoom;
use Tattler\Objects\TattlerConfig;
use Tattler\Base\Modules\ITattler;
use Tattler\Base\Channels\IChannel;
use Tattler\Base\Objects\ITattlerMessage;

use PHPUnit_Framework_TestCase;


require_once __DIR__ . '/../config.php';


class TattlerTest extends PHPUnit_Framework_TestCase
{
	/** @var ITattler $tattler */
	private $tattler;
	
	
	private function getConfig()
	{
		$result = new TattlerConfig();
		$result->Namespace = 'Test';
		$result->Port = 80;
		$result->Secure = false;
		$result->Server = 'localhost.domain.tld';
		$result->Secret = uniqid();
		
		return $result;
	}
	
	/**
	 * @return ITattlerMessage
	 */
	private function getDummyMessage()
	{
		/** @var ITattlerMessage $message */
		$message = Common::skeleton(ITattlerMessage::class);
		$message->setPayload(['some', 'random', 'data']);
		
		return $message;
	}
	
	
	protected function setUp()
	{
		parent::setUp();
		
		$this->tattler = Common::skeleton(ITattler::class);
		$this->tattler->setConfig($this->getConfig());
	}
	
	
	public function test_setConfig_should_return_static()
	{
		self::assertInstanceOf(ITattler::class, $this->tattler->setConfig($this->getConfig()));
	}
	
	public function test_getWsAddress_should_return_WS_address()
	{
		$result = $this->tattler->getWsAddress();
		$config = $this->getConfig();
		self::assertEquals('ws://' . $config->Server . ':' . $config->Port, $result);
	}
	
	public function test_getWsAddress_should_return_WSS_address()
	{
		$config = $this->getConfig();
		$config->Secure = true;
		$this->tattler->setConfig($config);
		$result = $this->tattler->getWsAddress();
		self::assertEquals('wss://' . $config->Server . ':' . $config->Port, $result);
	}
	
	public function test_getHttpAddress_should_return_HTTP_address()
	{
		$result = $this->tattler->getHttpAddress();
		$config = $this->getConfig();
		self::assertEquals('http://' . $config->Server . ':' . $config->Port, $result);
	}
	
	public function test_getHttpAddress_should_return_HTTPS_address()
	{
		$config = $this->getConfig();
		$config->Secure = true;
		$config->Port = null;
		$this->tattler->setConfig($config);
		$result = $this->tattler->getHttpAddress();
		self::assertEquals('https://' . $config->Server . ':' . ITattler::DEFAULT_SECURE_PORT, $result);
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
}
