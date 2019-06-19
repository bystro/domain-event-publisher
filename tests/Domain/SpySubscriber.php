<?php
declare(strict_types=1);
namespace Test\Domain;

use Bystro\DomainEventPublisher\Domain\DomainEvent;
use Bystro\DomainEventPublisher\Domain\DomainEventSubscriber;

class SpySubscriber implements DomainEventSubscriber
{

    public /* DomainEvent */ $domainEvent;
    public /* bool */ $isHandled = false;
    private /* string */ $eventName;

    public function __construct(string $eventName)
    {
        $this->eventName = $eventName;
    }

    public function isSubscribedTo(DomainEvent $domainEvent): bool
    {
        return $this->eventName === $domainEvent->name;
    }

    public function handle(DomainEvent $domainEvent): void
    {
        $this->domainEvent = $domainEvent;
        $this->isHandled = true;
    }
}
