<?php
/**
 * This file is part of the cast package.
 *
 * Copyright (c) 2013 Jason Coward <jason@opengeek.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cast\Git\Commands;

use Cast\Git\Git;

abstract class GitCommand
{
    public $git;

    protected $command;
    protected $response;

    abstract public function run(array $args = array());

    public function __construct(Git &$git)
    {
        $this->git = &$git;
    }

    public function arg($key, $args, $default = false) {
        $value = $default;
        if (is_array($args) && array_key_exists($key, $args)) {
            $value = $args[$key];
            if (is_string($value)) {
                $value = escapeshellarg($value);
            }
        }
        return $value;
    }
}
