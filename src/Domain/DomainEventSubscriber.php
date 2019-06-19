<?php
declare(strict_types=1);
namespace Bystro\DomainEventPublisher\Domain;

interface DomainEventSubscriber
{

    public function handle(DomainEvent $domainEvent): void;

    public function isSubscribedTo(DomainEvent $domainEvent): bool;
}
