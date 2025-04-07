<?php

use PHPUnit\Framework\TestCase;
use UniversalTaskTracker\DTO\TrackerResponse;

class TrackerResponseTest extends TestCase
{
    public function testSuccessResponse()
    {
        $response = TrackerResponse::success('Operation completed', ['id' => 1]);

        $this->assertTrue($response->success);
        $this->assertEquals('Operation completed', $response->message);
        $this->assertIsArray($response->data);
        $this->assertEquals(1, $response->data['id']);
    }

    public function testErrorResponse()
    {
        $response = TrackerResponse::error('Something went wrong');

        $this->assertFalse($response->success);
        $this->assertEquals('Something went wrong', $response->message);
        $this->assertNull($response->data);
    }
}
