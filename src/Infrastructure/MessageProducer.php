<?php
declare(strict_types=1);
namespace Bystro\DomainEventPublisher\Infrastructure;

interface MessageProducer
{

    public function open(string $exchangeName);

    public function send(string $notificationBody, string $notificationType, string $notificationId, \DateTimeImmutable $notificationOccurredOn): void;

    public function close(string $exchangeName): void;
}
