<?php
/**
 * This file is part of the cast package.
 *
 * Copyright (c) Jason Coward <jason@opengeek.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cast\Controllers;


use Cast\Response\CastResponse;

class ControllerResponse
{
    protected $command;
    protected $arguments;
    protected $options;
    protected $response;

    public function __construct(ControllerInterface $controller, CastResponse &$response)
    {
        $this->command = $controller->getCommand();
        $this->arguments = $controller->getArguments();
        $this->options = $controller->getOptions();
        $this->response = &$response;
    }

    public function wasSuccessful()
    {
        return $this->response->wasSuccessful();
    }

    public function getCode()
    {
        return $this->response->getResult();
    }

    public function getOutput()
    {
        return $this->response->getOutput();
    }

    public function getErrors()
    {
        return $this->response->getErrors();
    }

    public function hasErrors()
    {
        return $this->response->hasErrors();
    }

    public function __toString()
    {
        return $this->response->__toString();
    }
}
