<?php

namespace Sid\Phalcon\ForwardExceptions\Test\Unit;

use Codeception\TestCase\Test;

use Exception;
use Phalcon\Di\FactoryDefault;
use Phalcon\Mvc\Dispatcher;

class EventTest extends Test
{
    public function testActionForwardsToAnother()
    {
        $di = new FactoryDefault();

        $di->set(
            "dispatcher",
            function () use ($di) {
                $dispatcher = new Dispatcher();

                $eventsManager = $di->getShared("eventsManager");

                $eventsManager->attach(
                    "dispatch:beforeException",
                    new \Sid\Phalcon\ForwardExceptions\Event()
                );

                $dispatcher->setEventsManager($eventsManager);

                return $dispatcher;
            },
            true
        );

        $dispatcher = $di->get("dispatcher");



        $dispatcher->setControllerName("simple");
        $dispatcher->setActionName("one");

        $controller = $dispatcher->dispatch();

        $this->assertEquals(
            "two",
            $dispatcher->getActionName()
        );
    }
}
