<?php
declare(strict_types=1);
namespace Bystro\DomainEventPublisher\Domain;

class DomainEventPublisher
{

    private /* array */ $subscribers = [];
    private static /* DomainEventPublisher */ $instance = null;
    private /* int */ $id = 0;

    public static function instance()
    {
        if (null === static::$instance) {
            static::$instance = new self();
        }

        return static::$instance;
    }

    public function __clone()
    {
        throw new \BadMethodCallException('Clone is not supported');
    }

    public function subscribe(DomainEventSubscriber $domainEventSubscriber): int
    {
        $id = $this->id;
        $this->subscribers[$id] = $domainEventSubscriber;
        $this->id++;

        return $id;
    }

    public function ofId(int $id): ?DomainEventSubscriber
    {
        return isset($this->subscribers[$id]) ? $this->subscribers[$id] : null;
    }

    public function unsubscribe(int $id): void
    {
        unset($this->subscribers[$id]);
    }

    public function publish(DomainEvent $domainEvent): void
    {
        foreach ($this->subscribers as $aSubscriber) {
            if ($aSubscriber->isSubscribedTo($domainEvent)) {
                $aSubscriber->handle($domainEvent);
            }
        }
    }
}
