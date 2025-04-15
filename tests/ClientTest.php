<?php

namespace Newngapi\Ngapi\Tests;

use Newngapi\Ngapi\Client;
use PHPUnit\Framework\TestCase;

class ClientTest extends TestCase
{
    /** @var Client */
    private $client;

    protected function setUp()
    {
        $this->client = new Client(
            'https://api.example.com',
            'test_sn',
            'test_secret_key'
        );
    }

    public function testCreatePlayer()
    {
        $result = $this->client->createPlayer('ag', 'test123', 'CNY');
        $this->assertInternalType('array', $result);
        $this->assertArrayHasKey('code', $result);
        $this->assertArrayHasKey('msg', $result);
        $this->assertArrayHasKey('data', $result);
    }

    public function testInvalidPlayerId()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->client->createPlayer('ag', 'test', 'CNY');
    }

    public function testGetBalance()
    {
        $result = $this->client->getBalance('ag', 'test123', 'CNY');
        $this->assertInternalType('array', $result);
        $this->assertArrayHasKey('code', $result);
        $this->assertArrayHasKey('msg', $result);
        $this->assertArrayHasKey('data', $result);
    }

    public function testTransfer()
    {
        $result = $this->client->transfer('ag', 'test123', 'CNY', '100.00', '1');
        $this->assertInternalType('array', $result);
        $this->assertArrayHasKey('code', $result);
        $this->assertArrayHasKey('msg', $result);
        $this->assertArrayHasKey('data', $result);
    }

    public function testInvalidTransferAmount()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->client->transfer('ag', 'test123', 'CNY', '0', '1');
    }

    public function testInvalidTransferType()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->client->transfer('ag', 'test123', 'CNY', '100.00', '3');
    }
} 