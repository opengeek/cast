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

class GitMv extends GitCommand
{
    protected $command = 'mv';

    public function run(array $args = array())
    {
        $source = array_shift($args);
        $destination = array_shift($args);
        $args = array_shift($args);

        if ((!is_array($source) || !is_string($source)) || $source === '' || !is_string($destination) || $destination === '') {
            throw new \InvalidArgumentException("git mv requires at least one source and a destination argument");
        }

        $command = $this->command;
        if ($this->arg('dry-run', $args)) $command .= ' --dry-run';
        elseif ($this->arg('n', $args)) $command .= ' -n';
        if ($this->arg('force', $args)) $command .= ' --force';
        elseif ($this->arg('f', $args)) $command .= ' -f';
        if ($this->arg('verbose', $args)) $command .= ' --verbose';
        elseif ($this->arg('v', $args)) $command .= ' -v';
        if ($this->arg('k', $args)) $command .= ' -k';

        if (is_array($source)) $source = implode(" ", $source);
        $command .= " {$source}";
        $command .= " {$destination}";

        return $this->exec($command);
    }
}
