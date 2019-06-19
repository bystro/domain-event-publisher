<?php
declare(strict_types=1);
namespace Test\Domain;

use Bystro\DomainEventPublisher\Domain\DomainEvent;

class FakeDomainEvent implements DomainEvent
{

    public /* string */ $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function occurredOn(): \DateTimeImmutable
    {
        
    }
}
