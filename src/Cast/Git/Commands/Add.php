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

class Add extends GitCommand
{
    protected $command = 'add';

    public function run(array $args = array())
    {
        $pathSpec = array_shift($args);
        $args = array_shift($args);

        if ($pathSpec === null || (is_string($pathSpec) && $pathSpec === '') || (!is_string($pathSpec) && !is_array($pathSpec))) {
            $pathSpec = '.';
        }
        if (!is_array($pathSpec)) {
            $pathSpec = array($pathSpec);
        }
        $paths = implode(" ", $pathSpec);

        $command = $this->command;
        if ($this->arg('dry-run', $args)) {
            $command .= ' --dry-run';
            if ($this->arg('ignore-missing', $args)) $command .= ' --ignore-missing';
        }
        if ($this->arg('verbose', $args)) $command .= ' --verbose';
        if ($this->arg('force', $args)) $command .= ' --force';
        if ($this->arg('update', $args)) {
            $command .= ' --update';
        } elseif ($this->arg('all', $args)) {
            $command .= ' --all';
        } elseif ($this->arg('no-all', $args)) {
            $command .= ' --no-all';
        }
        if ($this->arg('intent-to-add', $args)) $command .= ' --intent-to-add';
        if ($this->arg('refresh', $args)) $command .= ' --refresh';
        if ($this->arg('ignore-errors', $args)) $command .= ' --ignore-errors';
        $command .= " {$paths}";

        $response = $this->git->exec($command);
        if ($response[0] !== 0 && !empty($response[2])) {
            throw new \RuntimeException($response[2]);
        }
        return $response[1];
    }
}
