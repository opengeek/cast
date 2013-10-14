<?php
/**
 * This file is part of the cast package.
 *
 * Copyright (c) Jason Coward <jason@opengeek.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cast\Git\Commands;


use Cast\CastException;

class GitCommandException extends CastException
{
    protected $command;

    public function __construct(GitCommand &$command, $message = '', $code = 0, \Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->command = &$command;
    }

    public function getCommand()
    {
        return $this->command;
    }
}
