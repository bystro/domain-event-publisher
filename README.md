domain-event-publisher
==================

## About
Publisher of domain events into RabbitMQ. It can be used to Bounded Context integrations.

Inspired and based on https://github.com/dddinphp/ddd

Thanks to: Carlos(https://github.com/carlosbuenosvinos), Christian(https://github.com/theUniC) nd Keyvan(https://github.com/keyvanakbary).

## Requirements
* PHP 7.1
* ext-bcmath
* ext-sockets(optionaly)

## Usage
1. Create and subscribe RabbitMQ aware subscriber.
```php
<?php

use Bystro\DomainEventPublisher\Domain\DomainEventPublisher;
use Bystro\DomainEventPublisher\Infrastructure\MessageProducerDomainEventSubscriber;
use Bystro\DomainEventPublisher\Infrastructure\RabbitMqMessageProducer;
use PhpAmqpLib\Connection\AMQPConnection;

$messageProducer = new RabbitMqMessageProducer(
        new AMQPConnection('127.0.0.1', 5672, 'guest', 'guest')
);
$messageProducer->open('example-exchange-name');

DomainEventPublisher::instance()->subscribe(
        new MessageProducerDomainEventSubscriber(
               $messageProducer 
        )
);

```

2. Create Domain Event
```php
<?php
namespace yourApp/namespace;

use Bystro\DomainEventPublisher\Domain\DomainEvent;

class FileSavedEvent implements DomainEvent
{
    private /* string */ $filename;
    private /* string */ $destinationPath;    
    private /* \DateTimeImmutable */ $occurredOn;

    public function __construct(string $filename, string $destinationPath)
    {
        $this->filename = $filename;
        $this->destinationPath = $destinationPath;
        $this->occurredOn = new \DateTimeImmutable();
    }

    public function filename(): string
    {
        return $this->filename;
    }
    
    public function destinationPath(): string
    {
        return $this->destinationPath;
    }

    public function occurredOn(): \DateTimeImmutable
    {
        return $this->occurredOn;
    }
}

```

3. Publish your FileSavedEvent Domain Event.
```php
<?php
namespace yourApp/namespace;

use Bystro\DomainEventPublisher\Domain\DomainEventPublisher;

class File
{

    private /* string */ $filename;
    private /* string */ $contents;

    public function __construct(string $filename, string $contents)
    {
        $this->filename = $filename;
        $this->contents = $contents;
    }
    
    public function save(string $destinationPath): void
    {        
        file_put_contents($destinationPath . $this->filename, $this->contents);
        
        DomainEventPublisher::instance()->publish(
            new FileSavedEvent($this->name, $destinationPath)
        );        
    }

}

```

FileSavedEvent is published in *example-exchange-name* queue.

## Install

### Composer
In command line
```
composer require bystro/domain-events-publisher
```
or composer.json
```
"require": {       
    "bystro/domain-events-publisher": "^1.0"
}
```
