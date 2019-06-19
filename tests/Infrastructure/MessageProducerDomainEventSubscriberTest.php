<?php
declare(strict_types=1);
namespace Test\Domain;

use PHPUnit\Framework\TestCase;
use PhpAmqpLib\Connection\AMQPConnection;
use Bystro\DomainEventPublisher\Infrastructure\RabbitMqMessageProducer;
use Bystro\DomainEventPublisher\Domain\DomainEventPublisher;
use Bystro\DomainEventPublisher\Infrastructure\MessageProducerDomainEventSubscriber;
use Test\Domain\FakeDomainEvent;

final class MessageProducerDomainEventSubscriberTest extends TestCase
{

    private const EXCHANGE_NAME = 'test-exchange-name';

    private /* DomainEventSubscriber */ $subscriber;

    public function setUp(): void
    {
        $messageProducer = new RabbitMqMessageProducer(
            new AMQPConnection('127.0.0.1', 5672, 'guest', 'guest')
        );
        $messageProducer->open(self::EXCHANGE_NAME);
        
        $this->subscriber = new MessageProducerDomainEventSubscriber($messageProducer);
    }

    public function testSendingMessage(): void
    {
        $exception = null;
        try {            
            DomainEventPublisher::instance()->subscribe($this->subscriber);

            DomainEventPublisher::instance()->publish(new FakeDomainEvent('test-event'));
        } catch (\Exception $exception) {
            var_dump($exception->getTraceAsString());            
        }

        $this->assertNull($exception);
    }
}
