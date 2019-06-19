<?php
declare(strict_types=1);
namespace Test\Domain;

use PHPUnit\Framework\TestCase;
use Bystro\DomainEventPublisher\Domain\DomainEvent;
use Bystro\DomainEventPublisher\Domain\DomainEventSubscriber;
use Bystro\DomainEventPublisher\Domain\DomainEventPublisher;

final class DomainEventPublisherTest extends TestCase
{

    public function testIfsubscriberIsSubscribed(): void
    {
        $subscriberId = $this->subscribe($subscriber = new SpySubscriber('test-event'));
        $this->assertNotNull($this->getSubscribedSubscriber($subscriberId));
    }

    public function testIfsubscriberIsUnsubscribed(): void
    {
        $subscriberId = $this->subscribe($subscriber = new SpySubscriber('test-event'));
        $this->assertNotNull($this->getSubscribedSubscriber($subscriberId));
        $this->unsubscribe($subscriberId);
        $this->assertNull($this->getSubscribedSubscriber($subscriberId));
    }

    public function testItShouldNotifySubscriber(): void
    {
        $this->subscribe($subscriber = new SpySubscriber('test-event'));
        $this->publish($domainEvent = new FakeDomainEvent('test-event'));

        $this->assertEventHandled($subscriber, $domainEvent);
    }

    public function testNotSubscribedSubscribersShouldNotBeNotified(): void
    {
        $this->subscribe($subscriber = new SpySubscriber('test-event'));
        $this->publish(new FakeDomainEvent('test-event-with-name-that-spysubscriber-not-notify'));

        $this->assertEventNotHandled($subscriber);
    }

    public function testNotEventPublisherCloneable(): void
    {
        $this->expectException('BadMethodCallException');
        DomainEventPublisher::instance()->__clone();
    }

    private function subscribe(DomainEventSubscriber $subscriber): int
    {
        return DomainEventPublisher::instance()->subscribe($subscriber);
    }

    private function unsubscribe(int $id): void
    {
        DomainEventPublisher::instance()->unsubscribe($id);
    }

    private function getSubscribedSubscriber(int $id): ?DomainEventSubscriber
    {
        return DomainEventPublisher::instance()->ofId($id);
    }

    private function publish(DomainEvent $domainEvent): void
    {
        DomainEventPublisher::instance()->publish($domainEvent);
    }

    private function assertEventHandled(DomainEventSubscriber $subscriber, DomainEvent $domainEvent): void
    {
        $this->assertTrue($subscriber->isHandled);
        $this->assertEquals($domainEvent, $subscriber->domainEvent);
    }

    private function assertEventNotHandled($subscriber): void
    {
        $this->assertFalse($subscriber->isHandled);
        $this->assertNull($subscriber->domainEvent);
    }
}
