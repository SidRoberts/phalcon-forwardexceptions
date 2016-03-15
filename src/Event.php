<?php

namespace Sid\Phalcon\ForwardExceptions;

class Event extends \Phalcon\Mvc\User\Plugin
{
    /**
     * @var array
     */
    protected $callbacks;



    /**
     * @param array $calbacks
     */
    public function __construct(array $callbacks = [])
    {
        $this->callbacks = $callbacks;
    }



    /**
     * @param \Phalcon\Events\Event        $event
     * @param \Phalcon\DispatcherInterface $dispatcher
     * @param \Exception                   $exception
     *
     * @return boolean
     */
    public function beforeException(\Phalcon\Events\Event $event, \Phalcon\DispatcherInterface $dispatcher, \Exception $exception)
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
                    $closure = \Closure::bind($this->callbacks[$callbackName], $this);

                    $closure($exception);
                }
            }
        }

        return false;
    }
}
