<?php
declare(strict_types=1);
namespace Bystro\DomainEventPublisher\Domain;

interface DomainEvent
{

    public function occurredOn(): \DateTimeImmutable;
}
