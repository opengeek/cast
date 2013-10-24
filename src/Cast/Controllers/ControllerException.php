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


use Cast\CastException;

class ControllerException extends CastException
{
    protected $controller;

    public function __construct(ControllerInterface &$controller = null, $message = "", $code = 0, \Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->controller = &$controller;
    }

    public function getController()
    {
        return $this->controller;
    }
}
