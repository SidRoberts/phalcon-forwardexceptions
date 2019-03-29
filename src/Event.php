<?php

namespace Sid\Phalcon\ForwardExceptions;

use Closure;
use Exception;
use Phalcon\DispatcherInterface;
use Phalcon\Events\Event as PhalconEvent;
use Phalcon\Mvc\User\Plugin;

class Event extends Plugin
{
    /**
     * @var array
     */
    protected $callbacks;



    public function __construct(array $callbacks = [])
    {
        $this->callbacks = $callbacks;
    }



    public function beforeException(PhalconEvent $event, DispatcherInterface $dispatcher, Exception $exception) : bool
    {
        $methodAnnotations = $this->annotations->getMethod(
            $dispatcher->getHandlerClass(),
            $dispatcher->getActiveMethod()
        );

        if (!$methodAnnotations->has("ForwardException")) {
            return true;
        }

        $annotation = $methodAnnotations->get("ForwardException");

        $forward = $annotation->getArgument(0);

        $dispatcher->forward($forward);

        if ($annotation->hasArgument(1)) {
            $callbackNames = $annotation->getArgument(1);

            foreach ($callbackNames as $callbackName) {
                if (isset($this->callbacks[$callbackName])) {
                    $closure = Closure::bind(
                        $this->callbacks[$callbackName],
                        $this
                    );

                    $closure($exception);
                }
            }
        }

        return false;
    }
}
