<?php
/**
 * This file is part of the cast package.
 *
 * Copyright (c) Jason Coward <jason@opengeek.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cast\Git;


use Cast\CastException;

class GitException extends CastException
{
    protected $git;

    public function __construct(Git &$git, $message = '', $code = 0, \Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->git = &$git;
    }

    public function getGit()
    {
        return $this->git;
    }
}
