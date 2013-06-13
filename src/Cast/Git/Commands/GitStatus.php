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

class GitStatus extends GitCommand
{
    protected $command = 'status';

    public function run(array $args = array())
    {
        $args = array_shift($args);

        $command = $this->command;
        if ($this->arg('short', $args)) $command .= ' --short';
        if ($this->arg('long', $args)) $command .= ' --long';
        if ($this->arg('branch', $args)) $command .= ' --branch';
        if ($this->arg('ignored', $args)) $command .= ' --ignored';
        if (($when = $this->arg('ignore-submodules', $args)) != false) {
            if ($when === true) {
                $command .= ' --ignore-submodules';
            } else {
                $command .= ' --ignore-submodules=' . $when;
            }
        }
        if ($this->arg('ignored', $args)) $command .= ' --ignored';
        if ($this->arg('porcelain', $args)) $command .= ' --porcelain';
        if ($this->arg('x', $args)) $command .= ' -x';

        $response = $this->git->exec($command);

        if ($response[0] !== 0 && !empty($response[2])) {
            throw new \RuntimeException($response[2]);
        }
        $output = explode("\n", $response[1]);
        array_pop($output);
        return implode("\n", $output);
    }
}
