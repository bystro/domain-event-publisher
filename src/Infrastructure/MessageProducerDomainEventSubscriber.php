<?php
declare(strict_types=1);
namespace Bystro\DomainEventPublisher\Infrastructure;

use Bystro\DomainEventPublisher\Domain\DomainEvent;
use Bystro\DomainEventPublisher\Domain\DomainEventSubscriber;
use Ramsey\Uuid\Uuid;
use JMS\Serializer\SerializerBuilder;
use JMS\Serializer\Serializer;

class MessageProducerDomainEventSubscriber implements DomainEventSubscriber
{

    private /* MessageProducer */ $messageProducer;

    public function __construct(MessageProducer $messageProducer)
    {
        $this->messageProducer = $messageProducer;
    }

    public function handle(DomainEvent $domainEvent): void
    {
        $notificationBody = $this->getSerializer()->serialize($domainEvent, 'json');
        $notificationType = get_class($domainEvent);
        $notificationId = $this->getNotyficationId();

        $this->messageProducer->send($notificationBody, $notificationType, $notificationId, $domainEvent->occurredOn());
    }

    public function isSubscribedTo(DomainEvent $domainEvent): bool
    {
        return true;
    }

    private function getNotyficationId(): string
    {
        return Uuid::uuid4()->toString();
    }

    private function getSerializer(): Serializer
    {
        if (null === $this->serializer) {
            $this->serializer = SerializerBuilder::create()->build();
        }

        return $this->serializer;
    }
}
