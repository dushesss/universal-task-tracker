<?php

use PHPUnit\Framework\TestCase;
use UniversalTaskTracker\Drivers\Bitrix24\Bitrix24Driver;
use UniversalTaskTracker\DTO\TrackerResponse;

class Bitrix24DriverTest extends TestCase
{
    public function testCreateTaskReturnsSuccessfulResponse()
    {
        $driver = new Bitrix24Driver();

        $response = $driver->createTask(['title' => 'Test']);

        $this->assertInstanceOf(TrackerResponse::class, $response);
        $this->assertTrue($response->success);
        $this->assertEquals('Task created in Bitrix24', $response->message);
        $this->assertArrayHasKey('id', $response->data);
    }

    public function testGetTaskReturnsMockedTask()
    {
        $driver = new Bitrix24Driver();
        $response = $driver->getTask('123');

        $this->assertTrue($response->success);
        $this->assertEquals('123', $response->data['id']);
    }
}