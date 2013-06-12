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

class Config extends Command
{
    protected $command = 'config';

    public function run(array $args = array())
    {
        return $this->get(array_shift($args), $args);
    }

    public function get($name, array $args = array())
    {
        if (!$this->git->isInitialized()) {
            throw new \BadMethodCallException();
        }
        $response = $this->git->exec("{$this->command} {$name}");
        $output = explode("\n", $response[1], 2);
        return array_shift($output);
    }
}
