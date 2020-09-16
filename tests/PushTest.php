<?php

namespace Tests;

use bIbI4k0\ApplePusher\Push;
use bIbI4k0\ApplePusher\Types\Alert;
use bIbI4k0\ApplePusher\Types\Background;
use bIbI4k0\ApplePusher\Types\PayloadInterface;
use PHPUnit\Framework\TestCase;

/**
 * Class PushTest
 * @package Tests
 */
class PushTest extends TestCase
{
    private const TEST_TOKEN = 'test token';

    /**
     * @param PayloadInterface $payload
     * @return Push
     */
    private function makePush(PayloadInterface $payload): Push
    {
        return new Push(self::TEST_TOKEN, $payload);
    }

    /**
     * @param string $title
     * @return Alert
     */
    private function makeAlertPayload(string $title = 'test title'): Alert
    {
        return new Alert($title);
    }

    public function testGetOptions(): void
    {
        $push = $this->makePush($this->makeAlertPayload());
        $push->setTopic('topic');
        $push->setExpiration(100);
        $push->setPriority(10);

        $options = $push->getOptions();

        $this->assertEquals([
            'topic' => 'topic',
            'type' => Alert::TYPE_ALERT,
            'priority' => 10,
            'expiration' => 100,
        ], $options);
    }

    public function testChangePayload(): void
    {
        $push = $this->makePush($this->makeAlertPayload());

        $options = $push->getOptions();
        $this->assertEquals(Alert::TYPE_ALERT, $options['type']);

        $push->setPayload(new Background());
        $options = $push->getOptions();
        $this->assertEquals(Alert::TYPE_BACKGROUND, $options['type']);
    }

    public function testCloneWithDevice(): void
    {
        $push = $this->makePush($this->makeAlertPayload());

        $anotherPush = $push->cloneWithDeviceToken('new token');

        $this->assertNotSame($push, $anotherPush);
        $this->assertEquals($push->getPayload()->jsonSerialize(), $anotherPush->getPayload()->jsonSerialize());
        $this->assertEquals($push->getOptions(), $anotherPush->getOptions());
        $this->assertNotEquals($push->getDeviceToken(), $anotherPush->getDeviceToken());
        $this->assertNotEquals($push->getUuid(), $anotherPush->getUuid());
    }
}