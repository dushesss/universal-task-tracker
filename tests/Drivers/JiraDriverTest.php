<?php

use PHPUnit\Framework\TestCase;
use UniversalTaskTracker\Drivers\Jira\JiraDriver;
use UniversalTaskTracker\DTO\TrackerResponse;

class JiraDriverTest extends TestCase
{
    public function testCreateTaskReturnsSuccessfulResponse()
    {
        $driver = new JiraDriver();

        $response = $driver->createTask(['title' => 'Test']);

        $this->assertInstanceOf(TrackerResponse::class, $response);
        $this->assertTrue($response->success);
        $this->assertEquals('Task created in Jira', $response->message);
        $this->assertArrayHasKey('id', $response->data);
    }

    public function testGetTaskReturnsMockedTask()
    {
        $driver = new JiraDriver();
        $response = $driver->getTask('123');

        $this->assertTrue($response->success);
        $this->assertEquals('123', $response->data['id']);
    }
}