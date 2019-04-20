<?php

namespace Tests;

use Exception;
use Phalcon\Di\FactoryDefault;
use Phalcon\Mvc\Dispatcher;

class EventCest
{
    public function actionForwardsToAnother(UnitTester $I)
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

                $dispatcher->setDefaultNamespace("Tests\\");

                return $dispatcher;
            },
            true
        );

        $dispatcher = $di->get("dispatcher");



        $dispatcher->setControllerName("simple");
        $dispatcher->setActionName("one");

        $controller = $dispatcher->dispatch();

        $I->assertEquals(
            "two",
            $dispatcher->getActionName()
        );
    }
}
