<?php
declare(strict_types=1);
namespace Test\Domain;

use PHPUnit\Framework\TestCase;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use Bystro\DomainEventPublisher\Infrastructure\RabbitMqMessageProducer;

final class RabbitMqMessageProducerTest extends TestCase
{

    private const EXCHANGE_NAME = 'test-exchange-name';

    private /* RabbitMqMessaging */ $messageProducer;

    public function setUp(): void
    {
        $this->messageProducer = new RabbitMqMessageProducer(
            new AMQPStreamConnection('127.0.0.1', 5672, 'guest', 'guest')
        );
        $this->messageProducer->open(self::EXCHANGE_NAME);
    }

    public function testSendingMessage(): void
    {
        $exception = null;
        try {
            $this->messageProducer->send('test-body', 'test-type', 'test-id', new \DateTimeImmutable());
            $this->messageProducer->close(self::EXCHANGE_NAME);
        } catch (\Exception $exception) {
            
        }

        $this->assertNull($exception);
    }

    public function testFailSendingMessageWhenConnectionClosed(): void
    {
        $this->messageProducer->close(self::EXCHANGE_NAME);

        $this->expectException('Bystro\DomainEventPublisher\Infrastructure\Exception\ConnectionNotEstablishedException');

        $this->messageProducer->send('test-body', 'test-type', 'test-id', new \DateTimeImmutable());
    }
}
