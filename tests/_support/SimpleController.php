<?php

namespace Tests;

use Exception;
use Phalcon\Mvc\Controller;

class SimpleController extends Controller
{
    /**
     * @ForwardException(["action": "two"])
     */
    public function oneAction()
    {
        throw new Exception();
    }

    public function twoAction()
    {
    }
}
