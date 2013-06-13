<?php
/**
 * This file is part of the cast package.
 *
 * Copyright (c) 2013 Jason Coward <jason@opengeek.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cast\Commands;

use Cast\Cast;

abstract class CastCommand
{
    /** @var Cast */
    public $cast;

    protected $command;
    protected $response;

    public function __construct(&$cast)
    {
        $this->cast = &$cast;
    }

    public function run(array $args = array())
    {
        return $this->cast->git->{$this->command}->run($args);
    }

    public function arg($key, $args, $default = false) {
        $value = $default;
        if (is_array($args) && array_key_exists($key, $args)) {
            $value = $args[$key];
        }
        return $value;
    }
}
