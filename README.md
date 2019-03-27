Sid\Phalcon\ForwardExceptions
=============================

Forward Exceptions within a Phalcon controller to an action of your choosing.



[![License](https://img.shields.io/github/license/SidRoberts/phalcon-forwardexceptions.svg?style=for-the-badge)]()

[![GitHub issues](https://img.shields.io/github/issues-raw/SidRoberts/phalcon-forwardexceptions.svg?style=for-the-badge)]()
[![GitHub pull requests](https://img.shields.io/github/issues-pr-raw/SidRoberts/phalcon-forwardexceptions.svg?style=for-the-badge)]()



## Installing ##

Install using Composer:

```json
{
    "require": {
        "sidroberts/phalcon-forwardexceptions": "dev-master"
    }
}
```

You'll need to add the event to the `dispatcher` DI service:

```php
use Exception;
use Phalcon\Mvc\Dispatcher;

$di->set(
    "dispatcher",
    function () use ($di) {
        $dispatcher = new Dispatcher();

        // ...

        $eventsManager = $di->getShared("eventsManager");

        /*
         * These are your callbacks - things that you'd like your Exception to
         * be able to do. They're optional - you don't have to define any.
         * `$this` refers to the `\Sid\Phalcon\ForwardExceptions\Event` class
         * which has access to the DI.
         */
        $callbacks = [
            "flash" => function (Exception $e) {
                $this->flash->error(
                    $e->getMessage()
                );
            }
        ];

        $eventsManager->attach(
            "dispatch:beforeException",
            new \Sid\Phalcon\ForwardExceptions\Event(
                $callbacks
            )
        );

        $dispatcher->setEventsManager($eventsManager);

        // ...

        return $dispatcher;
    },
    true
);
```



## Example ##

In this example, if an Exception is thrown in `ExampleController::loginSubmitAction()`, it will be forwarded to `ExampleController::loginAction()`.
The second Annotation parameter says which callbacks should be called.
In this example, the "flash" callback will be run.

### Controller ###

```php
use Exception;
use Phalcon\Mvc\Controller;

class ExampleController extends Controller
{
    public function loginAction()
    {
        // ...
    }

    /**
     * @ForwardException(["action": "login"], ["flash"])
     */
    public function loginSubmitAction()
    {
        // ...

        if (!$success) {
            throw new Exception($errorMessage);
        }
    }
}
```
