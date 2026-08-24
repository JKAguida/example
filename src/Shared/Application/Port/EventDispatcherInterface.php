<?php

namespace App\Shared\Application\Port;
use App\Shared\Domain\Event\DomainEventInterface;

interface EventDispatcherInterface {
    public function dispatch(DomainEventInterface $event) : void ;
    /** @param class-string $eventName */
    public function addListener(string $eventName, EventListenerInterface $listener):void;
}