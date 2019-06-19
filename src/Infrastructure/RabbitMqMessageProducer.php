<?php
declare(strict_types=1);
namespace Bystro\DomainEventPublisher\Infrastructure;

use PhpAmqpLib\Message\AMQPMessage;
use PhpAmqpLib\Connection\AMQPConnection;

class RabbitMqMessageProducer implements MessageProducer
{

    protected /* AMQPConnection */ $connection;
    protected /* AMQPChannel */ $channel;
    protected /* string */ $exchangeName;

    public function __construct(AMQPConnection $aConnection)
    {
        $this->connection = $aConnection;
        $this->channel = null;
    }

    public function open(string $exchangeName): void
    {
        $this->exchangeName = $exchangeName;
        $this->openChannel();
    }

    public function send(string $notificationBody, string $notificationType, string $notificationId, \DateTimeImmutable $notificationOccurredOn): void
    {
        if ($this->channel === null) {
            throw new Exception\ConnectionNotEstablishedException('Message producer connection seems not to be established. Check if open() method was executed before send() method.');
        }

        $this->channel->basic_publish(
            new AMQPMessage(
                $notificationBody,
                ['type' => $notificationType, 'timestamp' => $notificationOccurredOn->getTimestamp(), 'message_id' => $notificationId]
            ),
            $this->exchangeName
        );
    }

    public function close(string $exchangeName): void
    {
        $this->closeChannel();
        $this->connection->close();
    }

    private function openChannel(): void
    {
        if ($this->channel !== null) {
            return;
        }

        $channel = $this->connection->channel();
        $channel->exchange_declare($this->exchangeName, 'fanout', false, true, false);
        $channel->queue_declare($this->exchangeName, false, true, false, false);
        $channel->queue_bind($this->exchangeName, $this->exchangeName);

        $this->channel = $channel;
    }

    private function closeChannel(): void
    {
        $this->channel->close();
        $this->channel = null;
    }
}
